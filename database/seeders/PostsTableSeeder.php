<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Category\Models\Category;
use Modules\Post\Models\Package;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostType;
use Modules\Shipping\Models\ShippingState;
use Modules\User\Models\User;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates posts with all combinations of post types, categories, areas, and packages.
     * Title format: "{type} {category} في {city}"
     * Example: "للبيع شقة في السالمية"
     */
    public function run(): void
    {
        // Get all active data
        $postTypes = PostType::where('status', 1)->get();
        $categories = Category::where('status', 'active')->whereNull('parent_id')->get();
        $states = ShippingState::with('cities')->get();
        $packages = Package::where('status', 1)->get();
        $activeUsers = User::whereNull('deleted_at')->pluck('id')->toArray();

        if (empty($activeUsers)) {
            $this->command->warn('No active users found. Creating a default user...');
            $user = User::first();
            if ($user) {
                $activeUsers = [$user->id];
            } else {
                $this->command->error('No users found at all. Please create a user first.');
                return;
            }
        }

        $this->command->info('Starting to create posts...');
        $this->command->info("Post Types: {$postTypes->count()}");
        $this->command->info("Categories: {$categories->count()}");
        $this->command->info("States: {$states->count()}");
        $this->command->info("Packages: {$packages->count()}");
        $this->command->info("Active Users: " . count($activeUsers));

        $totalPosts = 0;
        
        // Multi-language descriptions
        $descriptions = [
            [
                'ar' => 'عقار مميز في موقع استراتيجي، قريب من جميع الخدمات. يتميز بالتصميم العصري والتشطيبات الفاخرة.',
                'en' => 'Premium property in a strategic location, close to all services. Features modern design and luxury finishes.',
            ],
            [
                'ar' => 'فرصة استثمارية ممتازة، موقع حيوي ومطلوب. مناسب للسكن أو الاستثمار.',
                'en' => 'Excellent investment opportunity in a vibrant and sought-after location. Suitable for living or investment.',
            ],
            [
                'ar' => 'عقار فاخر بمساحة واسعة، إطلالة رائعة. قريب من المدارس والمستشفيات.',
                'en' => 'Luxurious property with spacious area and great view. Close to schools and hospitals.',
            ],
            [
                'ar' => 'موقع متميز، تشطيب سوبر ديلوكس، جاهز للسكن فوراً. الأوراق كاملة.',
                'en' => 'Excellent location, super deluxe finishing, ready for immediate occupancy. All documents complete.',
            ],
            [
                'ar' => 'عرض خاص لفترة محدودة، عقار بسعر مناسب جداً. لا تفوت الفرصة.',
                'en' => 'Special limited-time offer, property at a very reasonable price. Don\'t miss this opportunity.',
            ],
        ];

        // Post type translations mapping
        $postTypeTranslations = [
            'للبيع' => 'For Sale',
            'للإيجار' => 'For Rent',
            'للايجار' => 'For Rent',
            'مطلوب' => 'Wanted',
            'للبدل' => 'For Exchange',
        ];

        // Category translations mapping
        $categoryTranslations = [
            'شقق' => 'Apartments',
            'بيوت' => 'Houses',
            'فلل' => 'Villas',
            'أراضي' => 'Lands',
            'عمارات' => 'Buildings',
            'محلات' => 'Shops',
            'مكاتب' => 'Offices',
            'مزارع' => 'Farms',
            'شاليهات' => 'Chalets',
            'استراحات' => 'Rest Houses',
        ];

        $prices = [15000, 25000, 35000, 50000, 75000, 100000, 150000, 200000, 250000, 300000, 500000];

        foreach ($postTypes as $postType) {
            foreach ($categories as $category) {
                foreach ($states as $state) {
                    // Get cities for this state
                    $cities = $state->cities;
                    
                    if ($cities->isEmpty()) {
                        // If no cities, use state only
                        $cities = collect([null]);
                    }

                    foreach ($cities as $city) {
                        // Create a post for each package
                        foreach ($packages as $package) {
                            $userId = $activeUsers[array_rand($activeUsers)];
                            $locationNameAr = $city ? $city->name_ar : $state->name_ar;
                            $locationNameEn = $city ? ($city->name_en ?? $city->name_ar) : ($state->name_en ?? $state->name_ar);
                            
                            // Get type and category names
                            $typeNameAr = $postType->name ?? $postType->name_ar ?? 'عقار';
                            $typeNameEn = $postTypeTranslations[$typeNameAr] ?? $postType->name_en ?? 'Property';
                            
                            $categoryNameAr = $category->name ?? $category->name_ar ?? 'عقار';
                            $categoryNameEn = $categoryTranslations[$categoryNameAr] ?? $category->name_en ?? 'Property';
                            
                            // Title format: "{type} {category} في {city/state}"
                            $titleAr = "{$typeNameAr} {$categoryNameAr} في {$locationNameAr}";
                            $titleEn = "{$typeNameEn} {$categoryNameEn} in {$locationNameEn}";
                            
                            // Get random description with both languages
                            $descriptionData = $descriptions[array_rand($descriptions)];
                            
                            // Random created_at within last 25 days
                            $randomDaysAgo = rand(0, 25);
                            $createdAt = now()->subDays($randomDaysAgo)->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                            
                            // Calculate dates based on package
                            $startDate = $createdAt;
                            $endDate = $createdAt->copy()->addDays($package->period_days ?? 30);
                            
                            $post = Post::create([
                                'uuid' => Str::uuid(),
                                'user_id' => $userId,
                                'category_id' => $category->id,
                                'post_type_id' => $postType->id,
                                'package_id' => $package->id,
                                'state_id' => $state->id,
                                'city_id' => $city?->id,
                                'title' => ['ar' => $titleAr, 'en' => $titleEn],
                                'description' => $descriptionData,
                                'price' => $prices[array_rand($prices)],
                                'mobile_number' => '+965' . rand(50000000, 99999999),
                                'status' => 'approved',
                                'start_date' => $startDate,
                                'end_date' => $endDate,
                                'is_paid' => !$package->is_free,
                                'views_count' => rand(0, 500),
                                'created_at' => $createdAt,
                                'updated_at' => $createdAt,
                            ]);

                            $totalPosts++;

                            if ($totalPosts % 100 === 0) {
                                $this->command->info("Created {$totalPosts} posts...");
                            }
                        }
                    }
                }
            }
        }

        $this->command->info("✅ Successfully created {$totalPosts} posts!");
        $this->command->info("All posts are WITHOUT images to test the placeholder feature.");
    }
}
