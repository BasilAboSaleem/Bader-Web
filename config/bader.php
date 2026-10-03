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
    | Governorates drawn on the impact map, projected from geoBoundaries PSE ADM2 (CC BY 4.0) into
    | map_view_box with north at the top. "center" is the governorate centroid as map_x/map_y
    | percentages. A region is linked to one of them from Dashboard → Regions.
    */
    'map_view_box' => [200, 250],

    'map_areas' => [
        'north_gaza' => [
            'center' => [82, 16],
            'points' => '168.3,66.9 171.2,66.2 174.7,63.7 183.1,59.9 185.0,57.5 187.3,56.3 187.3,54.0 193.4,46.6 194.0,41.8 153.0,8.0 132.6,38.3 137.1,41.4 144.1,47.9 145.3,48.4 144.4,50.7 143.5,50.4 140.9,52.0 147.0,55.5 146.5,56.2 147.2,57.8 149.3,56.9 149.8,57.5 151.6,56.4 157.3,62.2 159.3,59.3',
        ],
        'gaza_city' => [
            'center' => [64, 30],
            'points' => '132.6,38.3 122.2,51.9 120.8,53.0 119.4,52.8 120.0,53.8 116.4,59.4 89.1,92.3 96.4,89.5 101.5,89.8 102.5,89.4 103.3,90.0 103.7,92.5 104.8,93.6 110.8,95.3 111.7,96.2 110.1,97.6 110.6,98.3 110.0,98.0 108.9,99.0 112.4,105.0 113.0,107.8 114.1,107.9 114.3,108.5 113.1,109.0 113.4,109.9 112.2,110.1 112.5,111.3 117.9,113.0 117.8,114.2 121.1,110.7 123.8,105.9 128.7,103.4 143.3,86.7 145.9,85.5 148.6,81.3 163.7,68.2 168.3,66.9 159.3,59.3 157.3,62.2 151.6,56.4 149.8,57.5 149.3,56.9 147.2,57.8 146.5,56.2 147.0,55.5 140.9,52.0 143.5,50.4 144.4,50.7 145.3,48.4 144.1,47.9 137.1,41.4',
        ],
        'middle_area' => [
            'center' => [44, 47],
            'points' => '89.1,92.3 70.0,115.0 55.8,129.3 60.8,133.0 61.0,135.2 62.5,137.1 67.7,136.9 71.9,140.7 74.4,136.8 76.8,138.4 81.6,145.4 82.8,146.1 83.6,145.2 84.7,146.6 89.5,147.5 89.9,144.6 92.9,140.2 93.3,137.8 100.0,131.1 101.3,128.2 104.9,124.1 107.5,123.2 117.8,114.2 117.9,113.0 112.5,111.3 112.2,110.1 113.4,109.9 113.1,109.0 114.3,108.5 114.1,107.9 113.0,107.8 112.4,105.0 108.9,99.0 110.0,98.0 110.6,98.3 110.1,97.6 111.7,96.2 110.0,94.9 104.8,93.6 103.7,92.5 103.0,89.6 96.4,89.5',
        ],
        'khan_younis' => [
            'center' => [30, 68],
            'points' => '63.8,221.1 69.6,214.7 73.0,207.8 85.7,200.5 87.7,196.5 89.6,189.6 85.1,155.3 86.1,150.7 88.6,150.2 89.5,147.5 84.7,146.6 83.6,145.2 82.8,146.1 81.6,145.4 76.8,138.4 74.4,136.8 71.9,140.7 67.7,136.9 62.5,137.1 61.0,135.2 60.8,133.0 55.8,129.3 49.6,134.7 37.4,148.5 18.6,167.1 21.6,170.2 24.3,171.0 23.8,171.6 24.9,173.3 27.0,171.0 30.1,175.0 33.4,181.7 34.0,180.8 34.7,181.2 35.2,177.7 37.0,178.6 40.1,183.0 41.6,183.7 40.4,187.4 37.5,191.6 40.7,195.1 42.2,196.2 42.4,194.6 48.2,198.4 52.6,198.5 58.1,197.3 57.1,201.9 58.2,202.8 60.3,211.5 60.6,212.3 62.8,211.2 65.7,216.8 63.1,218.9',
        ],
        'rafah' => [
            'center' => [17, 82],
            'points' => '19.7,167.8 18.6,167.1 6.0,179.2 15.0,196.2 17.6,196.9 31.5,242.0 39.3,238.9 46.2,231.2 59.4,224.5 63.8,221.1 63.1,218.9 65.7,216.8 62.8,211.2 60.6,212.3 60.3,211.5 58.2,202.8 57.1,201.9 58.1,197.3 52.6,198.5 48.2,198.4 42.4,194.6 42.2,196.2 40.7,195.1 37.5,191.6 40.4,187.4 41.6,183.7 40.1,183.0 37.0,178.6 35.2,177.7 34.7,181.2 34.0,180.8 33.4,181.7 30.1,175.0 27.0,171.0 24.9,173.3 23.8,171.6 24.3,171.0 21.6,170.2',
        ],
    ],

    /*
    | Impact figures an admin can attach to each region (shown on the impact map and region page).
    */
    'region_metrics' => [
        'max' => 6,
        'icons' => ['users', 'droplet', 'gift', 'heart', 'hand-heart', 'building', 'sprout', 'shield', 'sparkle', 'calendar'],
    ],

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
