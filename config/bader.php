<?php

return [
    'locales' => ['ar', 'en'],

    'urgent' => [
        'enabled' => false,
        'text' => '',
        'url' => null,
    ],

    'assets' => [
        'logo_light' => 'brand/logo-light.jpg',
        'logo_dark' => 'brand/logo-dark1.jpg',
        'logo_dark_1080' => 'brand/logo-dark2-1080.jpg',
        'logo_dark_4500' => 'brand/logo-dark2-4500.jpg',
        'mark_star' => 'brand/mark-star.svg',
        'favicon' => 'brand/mark-star.svg',
    ],

    /*
    | Donation categories offered on the donate page and quick-give bar.
    | Labels live in lang files under "donation.category.{key}".
    */
    'donation_categories' => ['general', 'zakat', 'sadaqah', 'relief', 'orphans', 'sadaqah_jariyah'],

    /*
    | Default quick-give options, used until the team saves custom ones in Site Settings.
    */
    'quick_give' => [
        ['category' => 'zakat', 'amount' => 50, 'label_ar' => 'زكاة المال', 'label_en' => 'Zakat'],
        ['category' => 'relief', 'amount' => 25, 'label_ar' => 'إغاثة عاجلة', 'label_en' => 'Urgent relief'],
        ['category' => 'orphans', 'amount' => 40, 'label_ar' => 'كفالة يتيم', 'label_en' => 'Sponsor an orphan'],
        ['category' => 'sadaqah_jariyah', 'amount' => 10, 'label_ar' => 'صدقة جارية', 'label_en' => 'Ongoing charity'],
    ],

    /*
    | Zakat nisab is 85 grams of gold. The gold price per gram (USD) is editable in Site Settings.
    */
    'zakat' => [
        'nisab_gold_grams' => 85,
        'default_gold_price_per_gram' => 95,
    ],

    /*
    | Gift card designs available when donating on behalf of someone.
    */
    'gift_designs' => [
        'ramadan' => ['key' => 'ramadan', 'label_ar' => 'هدية رمضان', 'label_en' => 'Ramadan gift', 'from' => '#0a2e2f', 'to' => '#1f6b38', 'accent' => '#e1e56b'],
        'eid_fitr' => ['key' => 'eid_fitr', 'label_ar' => 'هدية عيد الفطر', 'label_en' => 'Eid al-Fitr gift', 'from' => '#1f6b38', 'to' => '#4d9a5e', 'accent' => '#f7f8ef'],
        'eid_adha' => ['key' => 'eid_adha', 'label_ar' => 'هدية عيد الأضحى', 'label_en' => 'Eid al-Adha gift', 'from' => '#5b3a1a', 'to' => '#a8773f', 'accent' => '#fbf1e1'],
        'sadaqah_jariyah' => ['key' => 'sadaqah_jariyah', 'label_ar' => 'صدقة جارية', 'label_en' => 'Ongoing charity', 'from' => '#15524d', 'to' => '#2b8170', 'accent' => '#d2d63e'],
        'mother' => ['key' => 'mother', 'label_ar' => 'هدية إلى أمي', 'label_en' => 'For my mother', 'from' => '#7a2e4a', 'to' => '#b25a7a', 'accent' => '#fde7ef'],
        'father' => ['key' => 'father', 'label_ar' => 'هدية إلى أبي', 'label_en' => 'For my father', 'from' => '#17345a', 'to' => '#2f5f8f', 'accent' => '#e6f0fb'],
    ],

    /*
    | Institutional pages editable from Dashboard → Pages. "sections" are the cards shown on the page,
    | "extras" are optional blocks that only appear once the team fills them in.
    */
    'institutional_pages' => [
        'about' => ['sections' => ['mission' => 'heart', 'work' => 'map-pin', 'independence' => 'shield'], 'extras' => ['president_speech', 'vision']],
        'impact' => ['sections' => ['verification' => 'check', 'operations' => 'building', 'reporting' => 'eye'], 'extras' => []],
        'partners' => ['sections' => ['local' => 'hand-heart', 'international' => 'globe', 'community' => 'users'], 'extras' => []],
        'volunteer' => ['sections' => ['field' => 'map-pin', 'skills' => 'sparkle', 'commitment' => 'shield'], 'extras' => []],
        'contact' => ['sections' => [], 'extras' => []],
        'faq' => ['sections' => [], 'extras' => []],
    ],

    /*
    | Cards per institutional page: "sections" above are the defaults until the team saves its own cards.
    */
    'page_cards' => [
        'max' => 9,
        'icons' => ['heart', 'hand-heart', 'shield', 'check', 'eye', 'globe', 'users', 'map-pin', 'building', 'sparkle', 'droplet', 'sprout', 'gift', 'calendar', 'info'],
    ],

    /*
    | Homepage sections below the hero, in their default order. "quick_give" always sits right under
    | the hero (it overlaps it), so it can be hidden but not moved.
    */
    'home_sections' => ['quick_give', 'regions', 'programs', 'projects', 'gift', 'news', 'map', 'sponsorship', 'ways_to_give', 'trust'],

    'max_donation_categories' => 12,

    /*
    | Social platforms shown in the footer once a URL is saved in Site Settings.
    */
    'social_platforms' => ['facebook', 'instagram', 'x', 'youtube', 'tiktok', 'telegram', 'linkedin'],

    /*
    | Public translation strings editable from Dashboard → Site texts, grouped by key prefix.
    | Count-dependent strings ("{1} …|[2,*] …") are edited one form at a time.
    */
    'editable_text_groups' => [
        'home' => ['home.'],
        'layout' => ['header.', 'nav.', 'footer.', 'brand.', 'cta.', 'common.'],
        'projects' => ['program_page.', 'campaigns_page.', 'campaign_page.', 'campaign.', 'region_page.', 'region.', 'facility_page.', 'project.', 'news_page.', 'story_page.'],
        'sponsorship' => ['sponsorship_page.', 'sponsorship.'],
        'giving' => ['donate_page.', 'donate_box.', 'donate_success.', 'gift_page.', 'donation.'],
        'zakat' => ['zakat_page.', 'zakat.'],
        'pages' => ['page.', 'about_page.', 'contact_page.', 'faq_page.', 'form_page.', 'form.', 'contact.', 'errors.'],
    ],
];
