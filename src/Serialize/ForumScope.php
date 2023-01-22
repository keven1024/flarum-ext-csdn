<?php

namespace Keven1024\CSDN\Serialize;


use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Locale\Translator;
use Flarum\Settings\SettingsRepositoryInterface;
use Keven1024\CSDN\Tool\Cool;


class ForumScope
{
    public function __invoke(ForumSerializer $serializer, array $attributes): array
    {
        $settings = resolve(SettingsRepositoryInterface::class);
        $translator = resolve(Translator::class);

        $attributes["floorVisibilityMainTitleText"] = $settings->get(Cool::addPrefix('floor_main_title_text'), $translator->trans(Cool::addTrans('floor_main_title_text')));
        $attributes["floorVisibilitySubTitleText"] = $settings->get(Cool::addPrefix('floor_sub_title_text'), $translator->trans(Cool::addTrans('floor_sub_title_text')));
        $attributes["floorVisibilityBackground"] = $settings->get(Cool::addPrefix('floor_background'), $translator->trans(Cool::addTrans('floor_background')));
        $attributes["floorVisibilityMainButtonClass"] = $settings->get(Cool::addPrefix('floor_main_button_class'), $translator->trans(Cool::addTrans('floor_main_button_class')));
        $attributes["floorVisibilitySubButtonClass"] = $settings->get(Cool::addPrefix('floor_sub_button_class'), $translator->trans(Cool::addTrans('floor_sub_button_class')));
        $attributes["floorVisibilityContentTypesetting"] = $settings->get(Cool::addPrefix('floor_content_typesetting'), $translator->trans(Cool::addTrans('floor_content_typesetting')));
        $attributes["floorVisibilityContentMask"] = $settings->get(Cool::addPrefix('floor_content_mask'), $translator->trans(Cool::addTrans('floor_content_mask')));
        $attributes["floorVisibilityBackgroundColorsList"] = json_decode($settings->get(Cool::addPrefix('floor_background_colors_list'), base64_decode($translator->trans(Cool::addTrans('floor_background_colors_list')))));
        $attributes["floorVisibilityBackgroundImagesList"] = json_decode($settings->get(Cool::addPrefix('floor_background_images_list'), base64_decode($translator->trans(Cool::addTrans('floor_background_images_list')))));
        $attributes["floorVisibilityBackgroundVideosList"] = json_decode($settings->get(Cool::addPrefix('floor_background_videos_list'), base64_decode($translator->trans(Cool::addTrans('floor_background_videos_list')))));
        $attributes["floorVisibilityButtonList"] = json_decode($settings->get(Cool::addPrefix('floor_button_list'), base64_decode($translator->trans(Cool::addTrans('floor_button_list')))));
        $attributes["floorVisibilityBackgroundFallback"] = $settings->get(Cool::addPrefix('floor_background_fallback'), $translator->trans(Cool::addTrans('floor_background_fallback')));

//        // 内容可见权限
        $attributes["contentVisibilityDivisibleCopyAll"] = Cool::toSerialize($settings->get(Cool::addPrefix('divisible_copy_all'), '0'));
        $attributes["contentVisibilityDivisibleRightKey"] = Cool::toSerialize($settings->get(Cool::addPrefix('divisible_right_key'), '0'));
        $attributes["contentVisibilityDivisibleF12"] = Cool::toSerialize($settings->get(Cool::addPrefix('divisible_f12'), '0'));
        $attributes["contentVisibilityDivisibleF12Tip"] = $settings->get(Cool::addPrefix('divisible_f12_tip'), $translator->trans(Cool::addTrans('divisible_f12_tip')));
        $attributes["contentVisibilityDivisibleCopyCode"] = Cool::toSerialize($settings->get(Cool::addPrefix('divisible_copy_code'), '0'));
        $attributes["contentVisibilityDivisibleCopyCodeTip"] = $settings->get(Cool::addPrefix('divisible_copy_code_tip'), $translator->trans(Cool::addTrans('divisible_copy_code_tip')));
        $attributes["contentVisibilityCopyAddCopyright"] = Cool::toSerialize($settings->get(Cool::addPrefix('copy_add_copyright'), '0'));
        $attributes["contentVisibilityAddCopyrightText"] = $settings->get(Cool::addPrefix('add_copyright_text'), $translator->trans(Cool::addTrans('add_copyright_text')));

        $attributes["contentVisibilityTipText"] = $settings->get(Cool::addPrefix('content_visibility_tips_text'), $translator->trans(Cool::addTrans('content_visibility_tips_text')));
        $attributes["contentVisibilityDivisibleEvent"] = Cool::toSerialize($settings->get(Cool::addPrefix('divisible_event'), '0'));
        $attributes["contentVisibilityDivisibleEventTips"] = json_decode($settings->get(Cool::addPrefix('divisible_event_tips'), base64_decode($translator->trans(Cool::addTrans('divisible_event_tips')))));

        $attributes["canSeeFloorAll"] = $serializer->getActor()->can('csdnFloorVisibility');
        $attributes["canSeePostBody"] = $serializer->getActor()->can('csdnContentVisibility');
        return $attributes;
    }
}
