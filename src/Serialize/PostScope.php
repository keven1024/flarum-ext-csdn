<?php

namespace Keven1024\CSDN\Serialize;

use Flarum\Extension\ExtensionManager;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Database\Eloquent\Builder;

class PostScope
{
    protected $settings;

    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }

    public function __invoke(User $actor, Builder $query)
    {
        $query->where(function (Builder $query) use ($actor) {
            /**
             * @var $manager ExtensionManager
             */
            $manager = resolve(ExtensionManager::class);
            $settings = resolve(SettingsRepositoryInterface::class);
            $floor_visibility = $settings->get('keven1024-csdn.floor_visibility', 'visible');
            // 标签扩展被禁用时的行为
            // 由于权限是全局的，所以不需要为讨论做子查询。
            if ($actor->hasPermission('discussion.csdnFloorVisibility')) {
                // 从技术上讲，我们不需要在这里做任何事情来返回所有的讨论
                // 但我们需要一个真实的语句，否则会扰乱下面的OR条件。
                $query->whereRaw('TRUE');
            } else {
                // 如果用户没有权限，强制隐藏一切
                if ($floor_visibility !== "visible") {
                    $query->whereRaw('FALSE');
                }
            }


            if ($floor_visibility != "divisible") {
                $trigger_floor_count = intval($settings->get('keven1024-csdn.trigger_floor_count', '1'));
                if ($floor_visibility === "conditional" && $trigger_floor_count !== 0) {
                    $floor_count = $trigger_floor_count + 1;
                    $query->orWhere('posts.number', '<', strval($floor_count));
                }
            }
        });
    }
}
