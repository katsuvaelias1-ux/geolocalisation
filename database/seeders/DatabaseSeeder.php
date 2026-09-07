<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\ArtisanProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Plomberie', 'Électricité', 'Menuiserie', 'Maçonnerie', 'Mécanique', 'Soudure', 'Couture', 'Informatique', 'Téléphonie', 'Peinture', 'Photographie', 'Opérateur de saisie', 'Électromécanique', 'Réparation électronique', 'Autres services'];
        $categories = collect($names)->mapWithKeys(function (string $name) {
            $category = Category::updateOrCreate(['slug' => Str::slug($name)], ['name' => $name, 'description' => "Services de {$name} à Butembo.", 'icon' => 'briefcase', 'status' => true]);
            return [$name => $category];
        });

        User::updateOrCreate(['email' => 'admin.demo@geoartisans.local'], ['name' => 'Administrateur', 'first_name' => 'Compte', 'role' => 'admin', 'status' => 'active', 'password' => Hash::make('GeoArtisans-Demo-2026')]);
        for ($index = 1; $index <= 5; $index++) {
            User::updateOrCreate(['email' => "client{$index}@geoartisans.local"], ['name' => "Client Fictif {$index}", 'first_name' => 'Utilisateur', 'role' => 'client', 'status' => 'active', 'password' => Hash::make('GeoArtisans-Demo-2026')]);
        }

        // Nettoyage des anciens comptes « Artisan Fictif ».
        // Les profils ci-dessous sont des exemples nommés, localisés à Butembo,
        // afin que la recherche reste utilisable avant les premières inscriptions réelles.
        User::query()->where('email', 'like', 'artisan%@geoartisans.local')->delete();

        $artisans = [
            ['first_name' => 'Jean', 'name' => 'Kambale', 'email' => 'jean.kambale@butembo.artisans', 'category' => 'Électromécanique', 'profession' => 'Électromécanicien – réparation d’imprimantes', 'commune' => 'Bulengera', 'quartier' => 'Kambali', 'cell' => 'Kambali I', 'address' => 'Avenue de l’Église, près du marché', 'latitude' => -0.1315, 'longitude' => 29.2895, 'profile_photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=85', 'cover_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=85'],
            ['first_name' => 'Aline', 'name' => 'Kasereka', 'email' => 'aline.kasereka@butembo.artisans', 'category' => 'Plomberie', 'profession' => 'Plombière – installations et dépannages', 'commune' => 'Kimemi', 'quartier' => 'Vighole', 'cell' => 'Vighole II', 'address' => 'Avenue du Cinquantenaire', 'latitude' => -0.1267, 'longitude' => 29.2868, 'profile_photo' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=400&q=85', 'cover_image' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189?auto=format&fit=crop&w=1200&q=85'],
            ['first_name' => 'David', 'name' => 'Mumbere', 'email' => 'david.mumbere@butembo.artisans', 'category' => 'Électricité', 'profession' => 'Électricien bâtiment', 'commune' => 'Mususa', 'quartier' => 'Matanda', 'cell' => 'Matanda I', 'address' => 'Boulevard Nyamwisi', 'latitude' => -0.1390, 'longitude' => 29.2740, 'profile_photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=400&q=85', 'cover_image' => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&w=1200&q=85'],
            ['first_name' => 'Grâce', 'name' => 'Kavira', 'email' => 'grace.kavira@butembo.artisans', 'category' => 'Couture', 'profession' => 'Couturière – vêtements et retouches', 'commune' => 'Vulamba', 'quartier' => 'Vulamba', 'cell' => 'Vulamba Centre', 'address' => 'Près de la paroisse', 'latitude' => -0.1172, 'longitude' => 29.3018, 'profile_photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=85', 'cover_image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?auto=format&fit=crop&w=1200&q=85'],
            ['first_name' => 'Patrick', 'name' => 'Mbusa', 'email' => 'patrick.mbusa@butembo.artisans', 'category' => 'Informatique', 'profession' => 'Technicien informatique', 'commune' => 'Bulengera', 'quartier' => 'Centre-ville', 'cell' => 'Centre-ville I', 'address' => 'Avenue du Commerce', 'latitude' => -0.1302, 'longitude' => 29.2921, 'profile_photo' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=400&q=85', 'cover_image' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=85'],
        ];

        foreach ($artisans as $artisan) {
            $user = User::updateOrCreate(
                ['email' => $artisan['email']],
                [
                    'first_name' => $artisan['first_name'],
                    'name' => $artisan['name'],
                    'phone' => '243000000000',
                    'full_address' => $artisan['address'],
                    'city' => 'Butembo',
                    'commune' => $artisan['commune'],
                    'quartier' => $artisan['quartier'],
                    'cell' => $artisan['cell'],
                    'role' => 'artisan',
                    'status' => 'active',
                    'profile_photo' => $artisan['profile_photo'],
                    'password' => Hash::make('GeoArtisans-Demo-2026'),
                ],
            );

            $category = $categories->get($artisan['category']);
            $profile = ArtisanProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id' => $category->id,
                    'slug' => Str::slug($artisan['first_name'] . '-' . $artisan['name']),
                    'profession' => $artisan['profession'],
                    'description' => "Professionnel disponible à {$artisan['commune']}, Butembo.",
                    'experience_years' => 5,
                    'commune' => $artisan['commune'],
                    'quartier' => $artisan['quartier'],
                    'address' => $artisan['address'],
                    'latitude' => $artisan['latitude'],
                    'longitude' => $artisan['longitude'],
                    'availability' => 'available',
                    'verification_status' => 'verified',
                    'cover_image' => $artisan['cover_image'],
                ],
            );
            $profile->services()->updateOrCreate(
                ['name' => $artisan['profession']],
                ['category_id' => $category->id, 'description' => "Service proposé à {$artisan['commune']}."],
            );
        }
    }
}
