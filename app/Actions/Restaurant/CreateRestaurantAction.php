<?php

namespace App\Actions\Restaurant;

use App\DTOs\Restaurant\RestaurantData;
use App\Models\Restaurant;
use App\Repositories\Contracts\RestaurantRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;

class CreateRestaurantAction
{
    public function __construct(
        protected RestaurantRepositoryInterface $restaurantRepository
    ) {}

    public function execute(RestaurantData $data): Restaurant
    {
        return DB::transaction(function () use ($data) {
            try {
                // Prepare restaurant data
                $restaurantData = $data->toArray();

                // Handle image uploads
                $restaurantData['image_url'] = $this->handleImageUpload($data->image_url, 'restaurants/images');
                $restaurantData['cover_image_url'] = $this->handleImageUpload($data->cover_image_url, 'restaurants/covers');

                // Generate slug
                $restaurantData['slug'] = $this->generateUniqueSlug($data->restaurant_name);

                // Set default status
                $restaurantData['status'] = Restaurant::STATUS_PENDING;
                $restaurantData['onboarding_step'] = 1;
                $restaurantData['setup_completed'] = false;

                // Create restaurant
                $restaurant = $this->restaurantRepository->create($restaurantData);

                // Log activity
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($restaurant)
                    ->log('Restaurant created');

                return $restaurant;

            } catch (Exception $e) {
                Log::error('Restaurant creation failed: '.$e->getMessage(), [
                    'data' => $data->toArray(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    private function handleImageUpload($file, string $directory): ?string
    {
        if (! $file) {
            return null;
        }

        try {
            // Ensure directory exists
            if (! Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory, 0755, true);
            }

            // Store file and return path (not full URL)
            return $file->store($directory, 'public');

        } catch (Exception $e) {
            Log::error("Image upload failed: {$e->getMessage()}");
            throw new Exception("Failed to upload image: {$e->getMessage()}");
        }
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (Restaurant::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }
}
