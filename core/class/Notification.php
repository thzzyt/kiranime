<?php

/**
 * This is class for user notification
 *
 * @package   Kiranime
 * @since   1.0.0
 * @link      https://kiranime.moe
 * @author    Dzul Qurnain
 * @license   GPL-2.0+
 */
class Kiranime_Notification
{
    public function __construct()
    {}

    public static function notify($episode_id = 0, $anime_id = 0, $episode_number = 0)
    {
        $anime_id = isset($anime_id) ? $anime_id : get_post_meta($episode_id, 'kiranime_episode_parent_id', true);

        $subscribers = Watchlist::by_anime($anime_id);

        foreach ($subscribers as $subscriber) {
            $get = get_posts([
                'post_type' => 'notification',
                'author'    => $subscriber,
            ]);

            if (!$get || count($get) == 0) {
                $create_notification = wp_insert_post([
                    'post_type'   => 'notification',
                    'post_title'  => 'notification_for_' . $subscriber,
                    'post_status' => 'publish',
                    'post_author' => $subscriber,
                ]);

                update_post_meta($create_notification, 'notification_update', json_encode([['episode_id' => $episode_id, 'status' => false, 'anime_id' => $anime_id, 'notification_id' => $episode_id . $anime_id, 'number' => $episode_number]]));
            } else {
                $meta = get_post_meta($get[0]->ID, 'notification_update', true);
                $meta = $meta ? json_decode($meta, true) : [];

                if (!is_array($meta)) {
                    $meta = [];
                }

                $in_array = array_filter($meta, function ($val) use ($episode_id) {
                    return $val['episode_id'] == $episode_id;
                });

                if (count($in_array) > 0) {
                    return;
                }

                $meta[] = [
                    'episode_id'      => $episode_id,
                    'status'          => false,
                    'anime_id'        => $anime_id,
                    'notification_id' => 'es' . $episode_id . 'nea' . $anime_id,
                    'number'          => $episode_number,
                ];

                update_post_meta($get[0]->ID, 'notification_update', json_encode($meta));

            }
        }
    }

    public static function checked($notification_id, $user_id)
    {
        if (!is_array($notification_id) || count($notification_id) === 0) {
            return ['results' => []];
        }

        $get = get_posts([
            'post_type' => 'notification',
            'author'    => $user_id,
        ]);

        $meta = get_post_meta($get[0]->ID, 'notification_update', true);
        $meta = $meta ? (array) json_decode($meta, true) : [];

        $new_notif = [];
        foreach ($meta as $n) {
            if (!in_array($n['notification_id'], $notification_id)) {
                $new_notif[] = $n;
            }
        }

        update_post_meta($get[0]->ID, 'notification_update', json_encode($new_notif));

        return ['results' => $new_notif];
    }

    public static function get_anime($notifications)
    {
        if (!is_array($notifications) || count($notifications) == 0) {
            return false;
        }

        $result = [];
        foreach ($notifications as $notif) {
            $episode = get_post($notif->episode_id);
            $anime   = get_post($notif->anime_id);

            $result[] = [
                'title'     => $anime->post_title,
                'anime_id'  => $notif->anime_id,
                'url'       => get_post_permalink($episode->ID),
                'published' => human_time_diff(get_the_time('U', $episode)),
                'number'    => get_post_meta($episode->ID, 'kiranime_episode_number', true),
            ];
        }

        return $result;
    }

    public static function get()
    {
        $uid = get_current_user_id();

        $get = get_posts([
            'post_type' => 'notification',
            'author'    => $uid,
        ]);

        $meta = isset($get) && isset($get[0]->ID) ? get_post_meta($get[0]->ID, 'notification_update', true) : false;
        $meta = $meta ? (array) json_decode($meta) : [];

        $results = [];
        foreach ($meta as $notif) {
            $episode = new Episode($notif->episode_id);

            if ($episode->empty) {
                continue;
            }
            $episode->get_meta('number', 'parent_id');
            if ($notif->anime_id) {
                $anime = new Anime($notif->anime_id);
            } elseif ($episode->meta['parent_id']) {
                $anime = new Anime($episode->meta['parent_id']);
            } else {
                Kiranime_Notification::delete($notif->notification_id, get_current_user_id());
                continue;
            }

            $anime->get_meta('featured');
            $results[] = [
                'title'     => $anime->post->post_title,
                'anime_id'  => $anime->anime_id,
                'url'       => $episode->url,
                'published' => human_time_diff(get_the_time('U', $notif->episode_id)),
                'number'    => $episode->meta['number'],
                'status'    => $notif->status,
                'featured'  => $anime->meta['featured'],
                'notif_id'  => $notif->notification_id,
            ];
        }

        return $results;

    }

    public static function delete($notification_id, $user_id)
    {
        if (!isset($notification_id)) {
            return ['data' => 'no notification to delete.', 'status' => 200];
        }

        $get = get_posts([
            'post_type' => 'notification',
            'author'    => $user_id,
        ]);

        if (count($get) === 0) {
            return ['data' => 'no notification to delete.', 'status' => 200];
        }

        $meta = isset($get) && isset($get[0]) && isset($get[0]->ID) ? get_post_meta($get[0]->ID, 'notification_update', true) : false;
        $meta = $meta ? (array) json_decode($meta, true) : [];

        $meta = array_filter($meta, function ($val) use ($notification_id) {
            return $notification_id != $val['notification_id'];
        });

        update_post_meta($get[0]->ID, 'notification_update', json_encode($meta));
        return ['data' => true, 'status' => 204];
    }
}
