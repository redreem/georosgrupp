<?php

class StoriesSQLHelper
{

    public static function selStories()
    {
        return "
            select
                s.id,
                i.id thumb_id,
                i.original_id,
                i.width,
                i.height,
                i.resize_type,
                i.quality,
                i.file_name,
                i.extension,
                i.mime_type,
                i.alt,
                s.likes,
                s.dislikes,
                s.content,
                s.video_duration,
                s.video_link
            from " . Core::$config['db']['base_new'] . ".stories s
            left join " . Core::$config['db']['base_new'] . ".images i on i.id = s.image_id
            where
                s.firm_id = :id_firm
            limit :start, :limit
        ";
    }

    public static function selStory()
    {
        return "
            select
                s.id,
                s.image_id,
                i.original_id,
                i.width,
                i.height,
                i.resize_type,
                i.quality,
                i.file_name,
                i.extension,
                i.mime_type,
                i.alt,
                s.created_at,
                s.author,
                s.likes,
                s.dislikes,
                s.content,
                s.video_duration,
                s.video_link
            from " . Core::$config['db']['base_new'] . ".stories s
            left join " . Core::$config['db']['base_new'] . ".images i on i.id = s.image_id
            where
                s.id = :id_story
        ";
    }

    public static function selSessionStory()
    {
        return "
            select s.session_id, s.story_id
            from " . Core::$config['db']['base_new'] . ".stories_likes s
            where
                s.session_id = ':session_id' and s.story_id = :story_id
        ";
    }

    public static function insSessionStory()
    {
        return "insert into " . Core::$config['db']['base_new'] . ".stories_likes (session_id, story_id) values (':session_id', :story_id)";
    }

    public static function updStoryThumb()
    {
        return "update " . Core::$config['db']['base_new'] . ".stories SET thumb_id = :thumb_id WHERE id = :id";
    }

    public static function updStoryImage()
    {
        return "update " . Core::$config['db']['base_new'] . ".stories SET image_id = :image_id WHERE id = :id";
    }

    public static function updStoryLikes()
    {
        return "update " . Core::$config['db']['base_new'] . ".stories SET likes = likes + 1 WHERE id = :id";
    }

    public static function updStoryDislikes()
    {
        return "update " . Core::$config['db']['base_new'] . ".stories SET dislikes = dislikes + 1 WHERE id = :id";
    }

}
