<?php

namespace Keven1024\CSDN\Serialize;

use Flarum\Api\Serializer\PostSerializer;
use Flarum\Extension\ExtensionManager;
use Flarum\Locale\Translator;
use Illuminate\Contracts\Filesystem\Factory;
use Flarum\Database\AbstractModel;
use Flarum\Settings\SettingsRepositoryInterface;
use Keven1024\CSDN\Tool\Cool;
use Flarum\Formatter\Formatter;
use Keven1024\CSDNImage\Process\DivisibleImage;

class ContentScope
{
    /**
     * @var \Illuminate\Contracts\Filesystem\Filesystem
     */
    private $uploadDir;

    private ExtensionManager $extensions;

    public function __construct(Factory $filesystemFactory,ExtensionManager $extensions)
    {
        $this->uploadDir = $filesystemFactory->disk('flarum-assets');
        $this->extensions = $extensions;
    }

    public function __invoke(PostSerializer $serializer, AbstractModel $post, array $attributes)
    {
        $settings = resolve(SettingsRepositoryInterface::class);
        $formatter = resolve(Formatter::class);
        $translator = resolve(Translator::class);

        $actor = $serializer->getActor();
        if (isset($attributes["contentHtml"])) {
            $attributes['isHideWord'] = False;
            $attributes['isThumbnail'] = False;
            $attributes['hideWordNum'] = 0;
            $content_visibility = $settings->get(Cool::addPrefix('content_visibility'), 'visible');


            if (!$actor->hasPermission('discussion.csdnContentVisibility') && $content_visibility !== 'visible') {
                $trigger_word_count = (int)$settings->get(Cool::addPrefix('trigger_word_count'), '0');
                $hidden_word_count = (int)$settings->get(Cool::addPrefix('hidden_word_count'), '0');
                $unparseHtml = $formatter->unparse($attributes['contentHtml']);
                $contentLen = mb_strlen($unparseHtml, 'utf-8');

                $contentHtml = '';
                $hideWordNum = 0;
                switch ($content_visibility) {
                    // 内容设为有条件可见
                    case 'conditional':
                        if ($contentLen >= $trigger_word_count) {
                            $scale = (1 - ($hidden_word_count * 0.01));
                            $showWordNum = intval($contentLen * $scale);
                            $contentHtml = Cool::blog_summary($attributes['contentHtml'], $showWordNum);
                            $hideWordNum = $contentLen - $showWordNum;
                        }
                        break;
                    // 不可见
                    case 'divisible':
                        $contentHtml = $this->outputHtml();
                        $hideWordNum = $contentLen;
                        break;
                }

                // 帖子图片处理
                $divisible_image = Cool::toSerialize($settings->get(Cool::addPrefix('divisible_image'), '0'));
                $csdn_image = $this->extensions->isEnabled('keven1024-csdn-image');
                if ($divisible_image && $csdn_image && class_exists("Keven1024\CSDNImage\Process\DivisibleImage")) {
                    $image = new DivisibleImage($this->uploadDir, $settings, $translator);
                    [$contentHtml, $attributes] = $image->divisible_image($contentHtml, $attributes, $post->id);
                }


                if ($contentHtml !== '') {
                    $attributes['contentHtml'] = $contentHtml;
                    $attributes['isHideWord'] = $hideWordNum !== 0; // 避免只劣化图片时显示
                    $attributes['hideWordNum'] = $hideWordNum;
                }
            }
        }
        return $attributes;
    }

    private function outputHtml()
    {
        $html = function ($css) {
            return "<div class='skeleton" . $css . "'></div>";
        };
        $result = "";
        for ($i = 0; $i < 3; $i++) {
            $result .= $html("");
        }
        for ($i = 0; $i < 3; $i++) {
            $result .= $html(" w-sm");
        }
        return $result;
    }


}
