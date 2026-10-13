<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fill in the public texts of Bader's four facilities and pick them for the home page. Images, gallery,
     * status and order stay as the team set them; the Quran academy is added if it does not exist yet.
     * Empty databases (fresh installs, tests) are left to the seeders.
     */
    public function up(): void
    {
        if (! DB::table('facilities')->exists()) {
            return;
        }

        $nextOrder = (int) DB::table('facilities')->max('order');

        foreach ($this->facilities() as $key => $texts) {
            $attributes = $texts + ['show_on_home' => true, 'updated_at' => now()];

            if (DB::table('facilities')->where('key', $key)->exists()) {
                DB::table('facilities')->where('key', $key)->update($attributes);

                continue;
            }

            DB::table('facilities')->insert($attributes + [
                'key' => $key,
                'location_ar' => 'قطاع غزة',
                'location_en' => 'Gaza Strip',
                'status' => 'published',
                'order' => ++$nextOrder,
                'created_at' => now(),
            ]);
        }
    }

    /**
     * The texts are content the team may keep editing, so they are not rolled back.
     */
    public function down(): void
    {
        //
    }

    /**
     * @return array<string, array{name_ar: string, name_en: string, description_ar: string, description_en: string, content_ar: string, content_en: string, capacity_ar: string, capacity_en: string}>
     */
    private function facilities(): array
    {
        return [
            'water_plant' => [
                'name_ar' => 'محطة بادر للتحلية',
                'name_en' => 'Bader Desalination Plant',
                'description_ar' => 'محطة لتحلية المياه تمدّ عشرات الآلاف من النازحين بمياه شرب نظيفة وآمنة، في وقتٍ صار فيه الحصول على الماء همّاً يومياً.',
                'description_en' => 'A desalination plant providing tens of thousands of displaced people with clean, safe drinking water at a time when finding water has become a daily struggle.',
                'content_ar' => implode("\n", [
                    'مع انهيار شبكات المياه وتلوّث الآبار في قطاع غزة، أصبح الماء النظيف من أصعب ما يمكن أن تحصل عليه الأسرة النازحة. من هنا وُلدت محطة بادر للتحلية، لتكون مصدراً ثابتاً لمياه الشرب الآمنة في قلب مناطق النزوح.',
                    'تعمل المحطة على تحلية المياه وتنقيتها لتصبح صالحة للشرب، ثم تصل إلى المخيمات ومراكز الإيواء والأحياء المحيطة، ليحصل الناس على الماء قريباً من أماكن سكنهم دون عناء الانتظار الطويل أو المشي لمسافات بعيدة.',
                    'يستفيد من المحطة عشرات الآلاف من النازحين، معظمهم من الأطفال والنساء وكبار السن، الذين يجدون في كل لتر ماء نظيف حمايةً من الأمراض المنقولة بالمياه، وراحةً من همٍّ يومي ثقيل.',
                    'يتابع فريق بادر تشغيل المحطة وصيانتها بشكل مستمر لضمان استمرار الضخ، ويحتاج ذلك إلى دعم متواصل لتوفير الوقود وقطع الغيار ومواد التنقية. كل تبرع للمحطة يعني ماءً نظيفاً يصل إلى أسرة نازحة اليوم.',
                ]),
                'content_en' => implode("\n", [
                    'With water networks destroyed and wells contaminated across the Gaza Strip, clean water has become one of the hardest things for a displaced family to find. Bader Desalination Plant was created to be a steady source of safe drinking water in the heart of the displacement areas.',
                    'The plant desalinates and purifies water until it is safe to drink, then delivers it to camps, shelters and surrounding neighbourhoods, so people can get water close to where they live without long queues or walking great distances.',
                    'Tens of thousands of displaced people benefit from the plant, most of them children, women and elderly people. For them, every litre of clean water means protection from waterborne diseases and relief from a heavy daily burden.',
                    'The Bader team runs and maintains the plant continuously to keep the water flowing, which depends on steady support for fuel, spare parts and purification materials. Every donation to the plant means clean water reaching a displaced family today.',
                ]),
                'capacity_ar' => 'تخدم عشرات الآلاف من النازحين',
                'capacity_en' => 'Serves tens of thousands of displaced people',
            ],
            'bakery' => [
                'name_ar' => 'مخبز بادر البلدي',
                'name_en' => 'Bader Community Bakery',
                'description_ar' => 'مخبز بلدي يوفّر الخبز لآلاف النازحين، ليبقى رغيف الخبز حاضراً على موائد الأسر رغم الحرب وشحّ الطحين.',
                'description_en' => 'A community bakery providing bread to thousands of displaced people, keeping a loaf on families\' tables despite the war and flour shortages.',
                'content_ar' => implode("\n", [
                    'في ظل الحرب وتوقّف كثير من المخابز وارتفاع أسعار الطحين، صار رغيف الخبز أمنيةً لكثير من الأسر النازحة في قطاع غزة. لذلك أنشأت بادر مخبزها البلدي ليكون سنداً يومياً للعائلات التي فقدت بيوتها ومصادر رزقها.',
                    'ينتج المخبز الخبز الطازج ويوزّعه على النازحين في المخيمات ومراكز الإيواء، مع إعطاء الأولوية للأسر الأشد حاجة كالأيتام والأرامل وكبار السن والمرضى، حفاظاً على كرامتهم وتخفيفاً عن كاهلهم.',
                    'خدم المخبز آلاف النازحين ووفّر لهم الخبز في أصعب الأيام، ويعمل فيه فريق من الخبازين والعمال من أبناء المنطقة، فكان مصدر غذاء للأسر ومصدر رزق للعاملين فيه في آنٍ واحد.',
                    'يعتمد استمرار عمل المخبز على توفير الطحين والوقود والخميرة وصيانة المعدات. بدعمك يصل الرغيف إلى أسرة نازحة كل صباح.',
                ]),
                'content_en' => implode("\n", [
                    'With the war shutting down many bakeries and flour prices soaring, a loaf of bread has become a wish for many displaced families in the Gaza Strip. Bader set up its community bakery to be a daily support for families who lost their homes and their livelihoods.',
                    'The bakery produces fresh bread and distributes it to displaced people in camps and shelters, giving priority to the families most in need, such as orphans, widows, the elderly and the sick, preserving their dignity and easing their burden.',
                    'The bakery has served thousands of displaced people, providing them with bread through the hardest days. It is run by a team of bakers and workers from the local community, making it both a source of food for families and a source of income for those who work there.',
                    'Keeping the bakery running depends on flour, fuel, yeast and equipment maintenance. With your support, bread reaches a displaced family every morning.',
                ]),
                'capacity_ar' => 'يخدم آلاف النازحين',
                'capacity_en' => 'Serves thousands of displaced people',
            ],
            'school' => [
                'name_ar' => 'مدرسة بادر التعليمية',
                'name_en' => 'Bader Educational School',
                'description_ar' => 'مدرسة تستقبل الطلبة من المرحلة الابتدائية حتى الثانوية، تخدم آلاف الطلبة وخرّجت آلافاً منهم، ليبقى العلم حاضراً رغم الحرب.',
                'description_en' => 'A school teaching students from primary through secondary level. It serves thousands of students and has graduated thousands more, keeping learning alive despite the war.',
                'content_ar' => implode("\n", [
                    'دمّرت الحرب كثيراً من مدارس قطاع غزة وحوّلت أخرى إلى مراكز إيواء، فوجد آلاف الأطفال أنفسهم بعيدين عن مقاعد الدراسة. أنشأت بادر مدرستها التعليمية إيماناً بأن التعليم ليس رفاهية، بل حقٌّ وطوق نجاة لجيلٍ كامل.',
                    'تضم المدرسة المراحل الدراسية من الابتدائية حتى الثانوية، ويقوم عليها طاقم من المعلمين والمعلمات الذين يقدّمون المنهاج الدراسي في بيئة آمنة ومنظّمة، مع اهتمام خاص بتعويض ما فات الطلبة من تعليم خلال الحرب.',
                    'لا يقتصر دور المدرسة على التعليم؛ فهي مساحة يستعيد فيها الطلبة شيئاً من حياتهم الطبيعية، ويجدون فيها الرعاية والأنشطة التي تعيد إليهم الأمل والثقة بالمستقبل.',
                    'خدمت المدرسة آلاف الطلبة وخرّجت آلافاً منهم ليواصلوا طريقهم نحو الجامعات والحياة العملية. دعمك للمدرسة يساعد في توفير الكتب والقرطاسية وتجهيز الفصول ودعم المعلمين، ليبقى العلم والتعليم مستمرين في غزة.',
                ]),
                'content_en' => implode("\n", [
                    'The war destroyed many schools in the Gaza Strip and turned others into shelters, leaving thousands of children out of the classroom. Bader founded its school in the belief that education is not a luxury but a right and a lifeline for an entire generation.',
                    'The school covers every stage from primary through secondary. Its teachers deliver the curriculum in a safe and organised environment, with special attention to helping students catch up on the learning they lost during the war.',
                    'The school is more than lessons: it is a place where students regain a sense of normal life and find the care and activities that restore their hope and confidence in the future.',
                    'The school has served thousands of students and graduated thousands more, who have gone on to university and working life. Your support helps provide books and stationery, equip classrooms and support teachers, so that learning continues in Gaza.',
                ]),
                'capacity_ar' => 'تخدم آلاف الطلبة',
                'capacity_en' => 'Serves thousands of students',
            ],
            'quran_academy' => [
                'name_ar' => 'أكاديمية بادر للقرآن الكريم',
                'name_en' => 'Bader Quran Academy',
                'description_ar' => 'أكاديمية تحتضن مئات الأطفال لتعليمهم القرآن الكريم وتحفيظه، وغرس القيم الإسلامية والأخلاق الحميدة في نفوسهم.',
                'description_en' => 'An academy welcoming hundreds of children to learn and memorise the Holy Quran and to grow with Islamic values and good character.',
                'content_ar' => implode("\n", [
                    'وسط قسوة الحرب والنزوح، يحتاج الأطفال إلى مكانٍ يمنحهم السكينة ويغذّي أرواحهم. من هنا جاءت أكاديمية بادر للقرآن الكريم، لتكون بيتاً للقرآن يجتمع فيه الأطفال على التلاوة والحفظ والتعلّم.',
                    'تقدّم الأكاديمية حلقات منتظمة لتعليم التلاوة وأحكام التجويد وتحفيظ القرآن الكريم، يشرف عليها محفّظون ومحفّظات، وتراعي أعمار الأطفال ومستوياتهم المختلفة، مع متابعة مستمرة لتقدّمهم في الحفظ.',
                    'وإلى جانب الحفظ، تهتم الأكاديمية بغرس القيم الإسلامية والأخلاق الحميدة في نفوس الأطفال، وتعزيز ثقتهم بأنفسهم، من خلال الدروس التربوية والمسابقات والأنشطة الهادفة، ويُكرَّم المتميزون والحفظة تشجيعاً لهم.',
                    'تخدم الأكاديمية مئات الأطفال، ودعمك يساعد في توفير المصاحف ومكافآت المحفّظين وتجهيز أماكن الحلقات، ليكبر جيلٌ يحمل القرآن في قلبه وسلوكه.',
                ]),
                'content_en' => implode("\n", [
                    'Amid the hardship of war and displacement, children need a place that brings them calm and nourishes their souls. Bader Quran Academy was created to be a home for the Quran, where children come together to recite, memorise and learn.',
                    'The academy runs regular circles teaching recitation, Tajweed and memorisation of the Holy Quran, led by male and female teachers. Lessons are suited to each age group and level, and every child\'s memorisation progress is followed closely.',
                    'Alongside memorisation, the academy instils Islamic values and good character in the children and builds their self-confidence through educational lessons, competitions and meaningful activities, with outstanding students and those who complete their memorisation honoured to encourage them.',
                    'The academy serves hundreds of children. Your support helps provide copies of the Quran, rewards for the teachers and spaces for the circles, so that a generation grows up carrying the Quran in its heart and conduct.',
                ]),
                'capacity_ar' => 'تخدم مئات الأطفال',
                'capacity_en' => 'Serves hundreds of children',
            ],
        ];
    }
};
