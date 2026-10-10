<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Story;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * News and activities imported from the Bader Facebook page export.
 *
 * New stories are created as drafts; re-running refreshes their content and media
 * but keeps the status and featured flag chosen in the dashboard.
 */
class FacebookNewsSeeder extends Seeder
{
    private const MEDIA_SOURCE = 'seeders/media/facebook-news';

    private const MEDIA_DIRECTORY = 'stories/facebook';

    private const VIDEO_URL = 'https://www.facebook.com/bader.gaza/videos/%s/';

    public function run(): void
    {
        $programIds = Program::pluck('id', 'key');

        foreach ($this->stories() as $data) {
            $story = Story::firstOrNew(['key' => $data['key']]);

            if (! $story->exists) {
                $story->status = 'draft';
                $story->is_featured = false;
            }

            $seededGallery = $this->publishGallery($data['key']);
            $otherPhotos = array_values(array_filter(
                $story->galleryPhotos(),
                fn (string $path): bool => ! str_starts_with($path, 'storage/'.self::MEDIA_DIRECTORY.'/'),
            ));

            $story->fill([
                'program_id' => $programIds[$data['program']] ?? null,
                'title_ar' => $data['title_ar'],
                'title_en' => $data['title_en'],
                'excerpt_ar' => $data['excerpt_ar'],
                'excerpt_en' => $data['excerpt_en'],
                'content_ar' => $data['content_ar'],
                'content_en' => $data['content_en'],
                'category_ar' => $data['category_ar'],
                'category_en' => $data['category_en'],
                'published_at' => $data['published_at'],
                'image' => $this->publish($data['key'].'.jpg'),
                'gallery' => array_merge($seededGallery, $otherPhotos) ?: null,
                'videos' => array_map(fn (string $videoId): string => sprintf(self::VIDEO_URL, $videoId), $data['videos']) ?: null,
            ])->save();
        }
    }

    /**
     * @return list<string>
     */
    private function publishGallery(string $key): array
    {
        $files = array_map('basename', File::glob(database_path(self::MEDIA_SOURCE.'/'.$key.'-*.jpg')));
        natsort($files);

        return array_values(array_map($this->publish(...), $files));
    }

    private function publish(string $file): string
    {
        $path = self::MEDIA_DIRECTORY.'/'.$file;

        Storage::disk('public')->put($path, File::get(database_path(self::MEDIA_SOURCE.'/'.$file)));

        return 'storage/'.$path;
    }

    /**
     * @return list<array{key: string, program: string, published_at: string, category_ar: string, category_en: string, title_ar: string, title_en: string, excerpt_ar: string, excerpt_en: string, content_ar: string, content_en: string, videos: list<string>}>
     */
    private function stories(): array
    {
        return [
            [
                'key' => 'cash-assistance-khan-younis-mawasi',
                'program' => 'social_protection',
                'published_at' => '2026-09-21',
                'category_ar' => 'إغاثة',
                'category_en' => 'Relief',
                'title_ar' => 'مساعدات نقدية للأسر النازحة المتعففة في مواصي خانيونس',
                'title_en' => 'Cash Assistance for Displaced Families in Al-Mawasi, Khan Younis',
                'excerpt_ar' => 'نفّذت مؤسسة بادر الإنسانية مشروع توزيع مساعدات نقدية على الأسر النازحة المتعففة في مخيمات النزوح بخانيونس، لمساندتها في مواجهة أعباء الحياة.',
                'excerpt_en' => 'Bader Humanitarian distributed cash assistance to displaced families in need across the Khan Younis displacement camps, helping them cope with daily hardship.',
                'content_ar' => "في مخيمات النزوح في خانيونس، حيث تشتد الحاجة، نفّذت مؤسسة بادر الإنسانية مشروع توزيع مساعدات نقدية على الأسر النازحة المتعففة، لمساندتها في مواجهة أعباء الحياة وتوفير بعض احتياجاتها الأساسية.\n\nوتواصل المؤسسة الوقوف إلى جانب أهلنا، والعمل حيث تكون الحاجة أكبر.",
                'content_en' => "In the displacement camps of Khan Younis, where the need is greatest, Bader Humanitarian carried out a cash assistance project for displaced families in need, supporting them in facing the burdens of daily life and covering some of their basic needs.\n\nThe foundation continues to stand by our people and to work wherever the need is greatest.",
                'videos' => ['1072239708752395'],
            ],
            [
                'key' => 'new-school-year-bader-school',
                'program' => 'education',
                'published_at' => '2026-09-19',
                'category_ar' => 'تعليم',
                'category_en' => 'Education',
                'title_ar' => 'انطلاق العام الدراسي الجديد في مدرسة بادر',
                'title_en' => 'A New School Year Begins at Bader School',
                'excerpt_ar' => 'استقبلت مدرسة بادر طلابها مع انطلاق العام الدراسي الجديد، لتستمر مسيرتهم التعليمية رغم كل الظروف.',
                'excerpt_en' => 'Bader School welcomed its students at the start of the new school year, so their education can continue despite everything.',
                'content_ar' => "استقبلت مدرسة بادر طلابها مع انطلاق العام الدراسي الجديد، وبدأت أولى أيام الدراسة بعودة الطلبة إلى مقاعدهم ومدرستهم.\n\nورغم كل ما مرّت به غزة، تستمر الحياة في مدارسها، وتبقى مدرسة بادر مساحةً للتعلّم وبناء المستقبل.\n\nعامٌ جديد نبدأه مع طلابنا، على أمل أن يكون مليئًا بالتعلّم والنجاح واللحظات الجميلة، ففي كل يوم دراسي أملٌ جديد.",
                'content_en' => "Bader School welcomed its students as the new school year got under way, and the first days of classes saw students return to their desks and their school.\n\nDespite everything Gaza has been through, life goes on in its schools, and Bader School remains a space for learning and for building the future.\n\nWe begin a new year with our students, hoping it will be full of learning, success and happy moments, because every school day brings new hope.",
                'videos' => ['1115988810999422'],
            ],
            [
                'key' => 'bader-school-summer-camp-closing',
                'program' => 'education',
                'published_at' => '2026-09-17',
                'category_ar' => 'تعليم',
                'category_en' => 'Education',
                'title_ar' => 'اختتام المخيم الصيفي للأطفال في مدرسة بادر',
                'title_en' => 'Bader School Wraps Up Its Children\'s Summer Camp',
                'excerpt_ar' => 'اختتمت مدرسة بادر المخيم الصيفي للأطفال بعد أيام من الفعاليات الترفيهية والتعليمية التي جمعت بين المتعة والتعلّم.',
                'excerpt_en' => 'Bader School closed its children\'s summer camp after days of recreational and educational activities that combined fun with learning.',
                'content_ar' => "اختتمت مدرسة بادر المخيم الصيفي للأطفال، بعد أيام مليئة بالفعاليات الترفيهية والتعليمية والأنشطة التي جمعت بين المتعة والتعلّم، ورسمت البسمة على وجوه أطفالنا.\n\nومع اقتراب العودة إلى المدارس وبدء الفصل الدراسي الجديد، نتمنى لأطفالنا عامًا مليئًا بالتعلّم والنجاح والأمل.",
                'content_en' => "Bader School brought its children's summer camp to a close after days full of recreational and educational activities that combined fun with learning and brought smiles to our children's faces.\n\nAs the return to school and the new term approach, we wish our children a year full of learning, success and hope.",
                'videos' => ['1395212729257695'],
            ],
            [
                'key' => 'rafah-municipality-delegation-visit',
                'program' => 'community_partnerships',
                'published_at' => '2026-09-17',
                'category_ar' => 'شراكات',
                'category_en' => 'Partnerships',
                'title_ar' => 'بادر تستقبل وفدًا من بلدية رفح لتعزيز الخدمات الإنسانية والاستعداد لفصل الشتاء',
                'title_en' => 'Bader Hosts a Rafah Municipality Delegation to Strengthen Relief Services Ahead of Winter',
                'excerpt_ar' => 'استقبلت مؤسسة بادر الإنسانية وفدًا من بلدية رفح برئاسة رئيس البلدية الدكتور أحمد الصوفي، لبحث تعزيز الخدمات المقدمة للأسر النازحة والاستعداد لفصل الشتاء.',
                'excerpt_en' => 'Bader Humanitarian hosted a delegation from Rafah Municipality, led by Mayor Dr. Ahmed Al-Sufi, to discuss strengthening services for displaced families and preparing for winter.',
                'content_ar' => "استقبلت مؤسسة بادر الإنسانية وفدًا من بلدية رفح برئاسة رئيس البلدية الدكتور أحمد الصوفي، في إطار تعزيز التعاون والتنسيق المشترك في المجال الإنساني والإغاثي.\n\nوناقش اللقاء سبل دعم الأسر النازحة وتعزيز الخدمات المقدمة لها، إلى جانب الاستعداد المبكر لفصل الشتاء واحتياجاته، بما يسهم في التخفيف من معاناة أهلنا النازحين.",
                'content_en' => "Bader Humanitarian hosted a delegation from Rafah Municipality led by Mayor Dr. Ahmed Al-Sufi, as part of strengthening cooperation and joint coordination in humanitarian and relief work.\n\nThe meeting discussed ways to support displaced families and improve the services provided to them, as well as early preparation for winter and its needs, to help ease the suffering of displaced families.",
                'videos' => [],
            ],
            [
                'key' => 'beach-learning-point-rehabilitation',
                'program' => 'education',
                'published_at' => '2026-09-16',
                'category_ar' => 'تعليم',
                'category_en' => 'Education',
                'title_ar' => 'صيانة وترميم نقطة تعليمية للأطفال على شاطئ غزة',
                'title_en' => 'Rehabilitating a Learning Point for Children on the Gaza Coast',
                'excerpt_ar' => 'قامت مؤسسة بادر الإنسانية بصيانة وترميم نقطة تعليمية للأطفال على شاطئ غزة، لتوفير مساحة آمنة تساعدهم على مواصلة تعليمهم.',
                'excerpt_en' => 'Bader Humanitarian repaired and rehabilitated a learning point for children on the Gaza coast, providing a safe space for them to continue their education.',
                'content_ar' => "في إطار جهودها المتواصلة في دعم التعليم، قامت مؤسسة بادر الإنسانية بصيانة وترميم نقطة تعليمية للأطفال على شاطئ غزة، لتوفير مساحة آمنة تساعدهم على مواصلة مسيرتهم التعليمية.\n\nفالتعليم لا يتوقف، والأمل لا يغيب.",
                'content_en' => "As part of its ongoing efforts to support education, Bader Humanitarian repaired and rehabilitated a learning point for children on the Gaza coast, providing a safe space that helps them continue their education.\n\nEducation does not stop, and hope does not fade.",
                'videos' => ['2963356457341107'],
            ],
            [
                'key' => 'cash-assistance-khan-younis-families',
                'program' => 'social_protection',
                'published_at' => '2026-09-16',
                'category_ar' => 'إغاثة',
                'category_en' => 'Relief',
                'title_ar' => 'توزيع مساعدات نقدية على العائلات النازحة في خانيونس',
                'title_en' => 'Cash Assistance Distributed to Displaced Families in Khan Younis',
                'excerpt_ar' => 'نفّذت مؤسسة بادر الإنسانية مشروع توزيع مساعدات نقدية على العائلات النازحة في خانيونس، لتخفيف جزء من أعباء الحياة عنها.',
                'excerpt_en' => 'Bader Humanitarian distributed cash assistance to displaced families in Khan Younis to ease part of their daily burden.',
                'content_ar' => "في خانيونس، حيث تشتد الحاجة، نفّذت مؤسسة بادر الإنسانية مشروع توزيع مساعدات نقدية على العائلات النازحة، لتكون إلى جانبهم وتخفف عنهم جزءًا من أعباء الحياة.\n\nوتأتي هذه المساعدات ضمن جهود المؤسسة المستمرة لمساندة الأسر النازحة.",
                'content_en' => "In Khan Younis, where the need is greatest, Bader Humanitarian carried out a cash assistance project for displaced families, standing by them and easing part of their daily burden.\n\nThis assistance is part of the foundation's ongoing efforts to support displaced families.",
                'videos' => ['3928437547293848'],
            ],
            [
                'key' => 'tawjihi-top-students-honoring',
                'program' => 'education',
                'published_at' => '2026-09-15',
                'category_ar' => 'تعليم',
                'category_en' => 'Education',
                'title_ar' => 'مدرسة بادر تكرّم الطلبة المتفوقين في الثانوية العامة',
                'title_en' => 'Bader School Honors Top-Achieving High School Graduates',
                'excerpt_ar' => 'أقامت مدرسة بادر حفل تكريم للطلبة المتفوقين في الثانوية العامة، بحضور الطلبة وذويهم وعدد من الوجهاء والضيوف.',
                'excerpt_en' => 'Bader School held a ceremony honoring its top high school graduates, attended by the students, their families and a number of community figures and guests.',
                'content_ar' => "في أجواء مميزة، أقامت مدرسة بادر حفل تكريم الطلبة المتفوقين في الثانوية العامة، بحضور الطلبة وذويهم وعدد من الوجهاء والضيوف.\n\nوشكّل الحفل مناسبةً للاحتفاء بإنجاز أبنائنا، والتأكيد على أن التعليم والتميّز يصنعان الأمل رغم كل الظروف.\n\nكل التهاني لطلبتنا المتفوقين، ومنها إلى الأعلى دائمًا.",
                'content_en' => "In a special atmosphere, Bader School held a ceremony honoring its top-achieving high school graduates, attended by the students, their families and a number of community figures and guests.\n\nThe ceremony was a moment to celebrate our students' achievement and to affirm that education and excellence create hope despite all circumstances.\n\nCongratulations to all our outstanding students, and onward and upward.",
                'videos' => ['1735701174206451'],
            ],
            [
                'key' => 'bader-desalination-plant',
                'program' => 'water',
                'published_at' => '2026-09-15',
                'category_ar' => 'مياه',
                'category_en' => 'Water',
                'title_ar' => 'محطة بادر لتحلية المياه تخدم آلاف النازحين في مخيمات غزة',
                'title_en' => 'Bader Desalination Plant Serves Thousands of Displaced People in Gaza\'s Camps',
                'excerpt_ar' => 'تواصل محطة بادر لتحلية المياه رسالتها الإنسانية بتوفير المياه المحلاة لآلاف النازحين في مخيمات غزة.',
                'excerpt_en' => 'The Bader Desalination Plant continues its humanitarian mission, providing desalinated water to thousands of displaced people in Gaza\'s camps.',
                'content_ar' => "من الماء تبدأ الحياة. تخدم محطة بادر لتحلية المياه آلاف النازحين في مخيمات غزة، وتواصل رسالتها الإنسانية بتوفير المياه المحلاة لمن هم بأمسّ الحاجة إليها.\n\nبادر… ماءٌ يصل، وأملٌ يستمر.",
                'content_en' => "Life begins with water. The Bader Desalination Plant serves thousands of displaced people in Gaza's camps and continues its humanitarian mission of providing desalinated water to those who need it most.\n\nBader: water that arrives, and hope that endures.",
                'videos' => ['1599396555316302'],
            ],
            [
                'key' => 'university-fees-first-batch-ptc',
                'program' => 'education',
                'published_at' => '2026-09-14',
                'category_ar' => 'تعليم',
                'category_en' => 'Education',
                'title_ar' => 'تسديد الرسوم الجامعية لطلبة كلية فلسطين التقنية – الدفعة الأولى من منحة بادر',
                'title_en' => 'Bader Scholarship Covers University Fees for Palestine Technical College Students (First Batch)',
                'excerpt_ar' => 'ضمن برنامج تسديد الرسوم عن الطلبة الجامعيين، نفّذت مؤسسة بادر الإنسانية الدفعة الأولى من منحتها لطلبة كلية فلسطين التقنية – دير البلح.',
                'excerpt_en' => 'Through its university fees programme, Bader Humanitarian delivered the first batch of its scholarship to students at Palestine Technical College in Deir al-Balah.',
                'content_ar' => "ضمن برنامج تسديد الرسوم عن الطلبة الجامعيين في قطاع غزة، نفّذت مؤسسة بادر الإنسانية مشروع تسديد الرسوم عن عدد من الطلبة الجامعيين بالتعاون مع كلية فلسطين التقنية – دير البلح، ضمن الدفعة الأولى من منحة بادر الإنسانية.\n\nويأتي المشروع دعمًا للطلبة ومساندةً لهم لاستمرار مسيرتهم التعليمية في ظل الظروف الصعبة التي يمر بها قطاع غزة.\n\nلأن التعليم حق، ولأن مستقبل غزة يبدأ من أبنائها.",
                'content_en' => "Through its programme to cover university fees for students in the Gaza Strip, Bader Humanitarian paid the fees of a number of university students in cooperation with Palestine Technical College in Deir al-Balah, as the first batch of the Bader Humanitarian Scholarship.\n\nThe project supports students so they can continue their studies despite the difficult conditions in the Gaza Strip.\n\nBecause education is a right, and Gaza's future begins with its young people.",
                'videos' => ['1101666599484346'],
            ],
            [
                'key' => 'bader-scholarship-second-batch-registration',
                'program' => 'education',
                'published_at' => '2026-09-14',
                'category_ar' => 'تعليم',
                'category_en' => 'Education',
                'title_ar' => 'فتح باب التسجيل في منحة بادر الإنسانية – الدفعة الثانية لطلبة كلية فلسطين التقنية',
                'title_en' => 'Registration Opens for the Bader Scholarship (Second Batch) at Palestine Technical College',
                'excerpt_ar' => 'أعلنت مؤسسة بادر الإنسانية، بالتعاون مع كلية فلسطين التقنية – دير البلح، فتح باب التسجيل في الدفعة الثانية من منحة بادر لتسديد جزء من الرسوم الدراسية.',
                'excerpt_en' => 'Bader Humanitarian, in cooperation with Palestine Technical College in Deir al-Balah, opened registration for the second batch of the Bader Scholarship, which covers part of students\' tuition fees.',
                'content_ar' => "تعلن مؤسسة بادر الإنسانية، بالتعاون مع كلية فلسطين التقنية – دير البلح، وضمن برنامج تسديد الرسوم عن الطلبة الجامعيين، عن فتح باب التسجيل للاستفادة من منحة بادر الإنسانية – الدفعة الثانية.\n\nوتأتي هذه المنحة مساهمةً في التخفيف من الأعباء المالية عن كاهل الطلبة وأسرهم، وضمان استمراريتهم في تحصيلهم الأكاديمي.\n\nالفئة المستهدفة: طلبة كلية فلسطين التقنية – دير البلح.\n\nطبيعة الدعم: تسديد جزء من الرسوم الدراسية للفصل الدراسي الحالي.\n\nللتسجيل، يرجى تعبئة النموذج عبر الرابط التالي مع الحرص على إدخال البيانات بدقة: https://forms.gle/WWXVPz3YoWJVi7Sb9",
                'content_en' => "Bader Humanitarian, in cooperation with Palestine Technical College in Deir al-Balah and as part of its university fees programme, announces that registration is open for the Bader Humanitarian Scholarship (second batch).\n\nThe scholarship aims to ease the financial burden on students and their families and help them continue their studies.\n\nWho can apply: students of Palestine Technical College, Deir al-Balah.\n\nType of support: payment of part of the tuition fees for the current semester.\n\nTo register, please fill in the form at the following link, making sure your details are accurate: https://forms.gle/WWXVPz3YoWJVi7Sb9",
                'videos' => [],
            ],
            [
                'key' => 'baby-formula-distribution-khan-younis',
                'program' => 'food',
                'published_at' => '2026-09-12',
                'category_ar' => 'إغاثة',
                'category_en' => 'Relief',
                'title_ar' => 'توزيع حليب الأطفال على مئات الأسر النازحة في خانيونس',
                'title_en' => 'Baby Formula Distributed to Hundreds of Displaced Families in Khan Younis',
                'excerpt_ar' => 'وزّعت مؤسسة بادر الإنسانية حليب الأطفال على مئات الأسر النازحة في خانيونس، لأن أصغر أطفالنا يستحقون العناية والأمان.',
                'excerpt_en' => 'Bader Humanitarian distributed baby formula to hundreds of displaced families in Khan Younis, because our youngest children deserve care and safety.',
                'content_ar' => "وزّعت مؤسسة بادر الإنسانية حليب الأطفال على مئات الأسر النازحة في خانيونس، حرصًا على تلبية احتياجات الرضّع والأطفال الصغار في ظل ظروف النزوح الصعبة.\n\nلأن أصغر أطفالنا يستحقون العناية والأمان.",
                'content_en' => "Bader Humanitarian distributed baby formula to hundreds of displaced families in Khan Younis, to help meet the needs of infants and young children in the difficult conditions of displacement.\n\nBecause our youngest children deserve care and safety.",
                'videos' => ['1458386313021657'],
            ],
            [
                'key' => 'water-trucking-deir-al-balah',
                'program' => 'water',
                'published_at' => '2026-09-12',
                'category_ar' => 'مياه',
                'category_en' => 'Water',
                'title_ar' => 'سقيا الماء للأسر النازحة في مخيمات دير البلح',
                'title_en' => 'Water Deliveries for Displaced Families in Deir al-Balah Camps',
                'excerpt_ar' => 'تواصل مؤسسة بادر الإنسانية جهودها في سقيا الماء للأسر النازحة في مخيمات النزوح بدير البلح.',
                'excerpt_en' => 'Bader Humanitarian continues delivering water to displaced families in the Deir al-Balah displacement camps.',
                'content_ar' => "تواصل مؤسسة بادر الإنسانية جهودها في سقيا الماء للأسر النازحة في مخيمات النزوح بدير البلح، لتوفير احتياج أساسي والتخفيف من معاناة النزوح.\n\nفالماء حاجة، وفي مخيمات النزوح يصبح وصوله حياة.",
                'content_en' => "Bader Humanitarian continues its water delivery efforts for displaced families in the Deir al-Balah displacement camps, providing a basic need and easing the hardship of displacement.\n\nWater is a necessity, and in the displacement camps its arrival means life.",
                'videos' => [],
            ],
        ];
    }
}
