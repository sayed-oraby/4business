<?php

namespace Modules\Post\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\Category\Models\Category;
use Modules\Post\Models\Post;

class PostQueryBuilder
{
    protected Builder $query;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->query = Post::query()->active();
    }

    /**
     * Build the query with all filters applied.
     */
    public function build(): Builder
    {
        return $this->query
            ->when(request()->show_in_home != null,function ($q) {
                if(request()->show_in_home == 1) {
                    $q->where('is_paid', 1)->active();
                } else {
                    $q->active();
                }
            })
            ->with(['user', 'category', 'postType', 'state', 'city', 'attachments']);
    }

    /**
     * Apply all filters from the request.
     */
    public function applyFilters(): self
    {
        return $this
            ->filterByCategory()
            ->filterByPostType()
            // ->filterByGender()
            // ->filterByExperience()
            // ->filterByNationality()
            // ->filterByAge()
            ->filterByCity()
            ->filterByState()
            ->filterByPrice()
            // ->filterBySkills()
            ->applySearch();
    }

    /**
     * Filter by category ID (includes posts from subcategories).
     * Supports both array and single value.
     */
    public function filterByCategory(): self
    {
        if ($this->request->filled('category_id')) {
            
            $categoryInput = $this->request->category_id;
            
            // Check if category_id is an array
            if (is_array($categoryInput)) {
                // Collect all descendant IDs for each category in the array
                $allCategoryIds = [];
                foreach ($categoryInput as $catId) {
                    $descendantIds = Category::getDescendantIdsFor((int) $catId);
                    $allCategoryIds = array_merge($allCategoryIds, $descendantIds);
                }
                // Remove duplicates
                $allCategoryIds = array_unique($allCategoryIds);
                
                $this->query->whereIn('category_id', $allCategoryIds);
            } else {
                // Handle single value
                $categoryId = (int) $categoryInput;
                
                // Get all descendant category IDs (includes the category itself and all children)
                $categoryIds = Category::getDescendantIdsFor($categoryId);
                
                $this->query->whereIn('category_id', $categoryIds);
            }
        }

        return $this;
    }

    /**
     * Filter by post type (supports array or single value).
     */
    public function filterByPostType(): self
    {
        if ($this->request->filled('type_id')) {
            $typeIds = $this->request->type_id;
            
            // Handle both array and single value
            if (is_array($typeIds)) {
                $this->query->whereIn('post_type_id', $typeIds);
            } else {
                $this->query->where('post_type_id', $typeIds);
            }
        }

        return $this;
    }

    /**
     * Filter by gender.
     */
    public function filterByGender(): self
    {
        if ($this->request->filled('gender')) {
            $this->query->where('gender', $this->request->gender);
        }

        return $this;
    }

    /**
     * Filter by years of experience range.
     */
    public function filterByExperience(): self
    {
        if ($this->request->filled('experince_from')) {
            $this->query->where('years_of_experience', '>=', $this->request->experince_from);
        }

        if ($this->request->filled('experince_to')) {
            $this->query->where('years_of_experience', '<=', $this->request->experince_to);
        }

        return $this;
    }

    /**
     * Filter by nationality (partial match).
     */
    public function filterByNationality(): self
    {
        if ($this->request->filled('nationality')) {
            $this->query->where('nationality', 'like', '%' . $this->request->nationality . '%');
        }

        return $this;
    }

    /**
     * Filter by age range (calculated from birthdate).
     */
    public function filterByAge(): self
    {
        if ($this->request->filled('age_from')) {
            $this->query->whereDate('birthdate', '<=', now()->subYears($this->request->age_from));
        }

        if ($this->request->filled('age_to')) {
            $this->query->whereDate('birthdate', '>=', now()->subYears($this->request->age_to));
        }

        return $this;
    }

    /**
     * Filter by city ID.
     * Supports both array and single value.
     */
    public function filterByCity(): self
    {
        if ($this->request->filled('city_id')) {
            $cityInput = $this->request->city_id;
            
            if (is_array($cityInput)) {
                $this->query->whereIn('city_id', $cityInput);
            } else {
                $this->query->where('city_id', $cityInput);
            }
        }

        return $this;
    }

    /**
     * Filter by state ID (via city relationship).
     * Supports both array and single value.
     */
    public function filterByState(): self
    {
        if ($this->request->filled('state_id')) {
            $stateInput = $this->request->state_id;
            
            if (is_array($stateInput)) {
                $this->query->whereIn('state_id', $stateInput);
            } else {
                $this->query->where('state_id', $stateInput);
            }
        }

        return $this;
    }

    /**
     * Filter by price range.
     */
    public function filterByPrice(): self
    {
        if ($this->request->filled('min_price')) {
            $this->query->where('price', '>=', $this->request->min_price);
        }

        if ($this->request->filled('max_price')) {
            $this->query->where('price', '<=', $this->request->max_price);
        }

        return $this;
    }

    /**
     * Filter by skills (array of skill names).
     */
    public function filterBySkills(): self
    {
        if ($this->request->filled('skills') && is_array($this->request->skills)) {
            $skills = $this->request->skills;

            $this->query->whereHas('skills', function ($q) use ($skills) {
                $q->where(function ($subQ) use ($skills) {
                    foreach ($skills as $skill) {
                        $subQ->orWhere('name->en', 'like', "%{$skill}%")
                             ->orWhere('name->ar', 'like', "%{$skill}%")
                             ->orWhere('slug', 'like', "%{$skill}%");
                    }
                });
            });
        }

        return $this;
    }

    /**
     * Apply advanced multi-keyword search across multiple fields.
     */
    public function applySearch(): self
    {
        if (!$this->request->filled('search')) {
            return $this;
        }

        $search = $this->request->input('search');
        $keywords = $this->parseSearchKeywords($search);

        foreach ($keywords as $keyword) {
            $keyword = trim($keyword);
            if (empty($keyword)) {
                continue;
            }

            $this->query->where(function ($q) use ($keyword) {
                $q->where('title->en', 'like', "%{$keyword}%")
                  ->orWhere('title->ar', 'like', "%{$keyword}%")
                  ->orWhere('description->en', 'like', "%{$keyword}%")
                  ->orWhere('description->ar', 'like', "%{$keyword}%")
                  ->orWhereHas('user', function ($uq) use ($keyword) {
                      $uq->where('name', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('category', function ($cq) use ($keyword) {
                      $cq->where('title->en', 'like', "%{$keyword}%")
                         ->orWhere('title->ar', 'like', "%{$keyword}%");
                  })
                  ->orWhereHas('postType', function ($tq) use ($keyword) {
                      $tq->where('name->en', 'like', "%{$keyword}%")
                         ->orWhere('name->ar', 'like', "%{$keyword}%");
                  });
                  //   ->orWhereHas('skills', function ($sq) use ($keyword) {
                  //       $sq->where('name->en', 'like', "%{$keyword}%")
                  //          ->orWhere('name->ar', 'like', "%{$keyword}%");
                  //   });
            });
        }

        return $this;
    }

    /**
     * Parse search input into keywords array.
     */
    protected function parseSearchKeywords(mixed $search): array
    {
        if (is_array($search)) {
            return $search;
        }

        // Split by space, comma, or Arabic comma
        return preg_split('/[\s,،]+/', $search, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Get paginated results.
     */
    public function paginate(int $perPage = 10)
    {
        return $this->query->latest()->paginate($perPage);
    }

    /**
     * Get the underlying query builder.
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Static factory method for fluent API.
     */
    public static function make(Request $request): self
    {
        return new self($request);
    }
}
