<?php

/*
 * This file is part of keven1024/csdn.
 *
 * Copyright (c) 2022 keven1024.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Keven1024\CSDN;



use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\PostSerializer;
use Flarum\Extend;
use Flarum\Post\Post;
use Keven1024\CSDN\Serialize\ContentScope;
use Keven1024\CSDN\Serialize\ForumScope;
use Keven1024\CSDN\Serialize\PostScope;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),
    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),
    new Extend\Locales(__DIR__.'/locale'),
    (new Extend\ModelVisibility(Post::class))
        ->scope(PostScope::class),
    (new Extend\ApiSerializer(PostSerializer::class))
        ->attributes(ContentScope::class),
    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attributes(ForumScope::class),
];
