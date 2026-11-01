<?php

namespace App\Http\Controllers\Community;

use App\Http\Controllers\Controller;
use App\Domain\Community\Models\Post;
use App\Domain\Community\Actions\Posts\CreatePostAction;
use App\Domain\Community\Actions\Posts\UpdatePostAction;
use App\Domain\Community\Actions\Posts\SubmitPostForModerationAction;
use App\Domain\Community\Actions\Posts\ArchivePostAction;
use App\Domain\Community\Actions\Posts\DeletePostAction;
use App\Domain\Community\Actions\Engagement\TogglePostLikeAction;
use App\Domain\Community\Services\PostService;
use App\Domain\Community\Services\PostCategoryService;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\MediaService;
use App\Domain\Media\DTO\UploadMediaDTO;
use App\Services\Company\ActiveContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Illuminate\Contracts\View\View;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService,
        protected PostCategoryService $categoryService,
        protected ActiveContextService $context,
        protected MediaService $mediaService,
        protected CreatePostAction $createPostAction,
        protected UpdatePostAction $updatePostAction,
        protected SubmitPostForModerationAction $submitPostForModerationAction,
        protected ArchivePostAction $archivePostAction,
        protected DeletePostAction $deletePostAction,
        protected TogglePostLikeAction $togglePostLikeAction,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | Public pages
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $locale = app()->getLocale();

        $categoryId = $request->integer('category') ?: null;

        $posts = $this->postService->getFeed(
            locale: $locale,
            categoryId: $categoryId,
            perPage: 12
        );

        $categories = $this->categoryService
            ->getCategoriesWithPublishedPostsCount();

        return view('community.index', compact(
            'posts',
            'categories',
            'categoryId'
        ));
    }

    public function show1(): View
    {
        $post = [
            'id' => 1,
            'slug' => 'creating-timeless-hospitality-spaces',
            'title' => 'Creating Timeless Spaces for Modern Hospitality',
            'excerpt' => 'Discover how carefully selected furniture, materials and thoughtful design can transform hospitality interiors into memorable experiences.',
            'content' => [
                'Hospitality design is about more than creating beautiful interiors. It is about shaping experiences, building atmosphere and making every guest feel welcome.',
                'From boutique hotels to contemporary restaurants, furniture plays an essential role in defining the character of a space. The right combination of materials, textures and proportions can turn an ordinary interior into something truly memorable.',
                'For this project, we focused on natural materials, timeless silhouettes and carefully selected furniture pieces that balance visual appeal with everyday functionality.',
                'Our approach brings together comfort, durability and refined aesthetics — creating spaces that remain relevant beyond seasonal trends.',
            ],
            'cover' => asset('images/home/banners/hotel.jpg'),
            'images' => [
                asset('images/home/banners/hero.jpg'),
                asset('images/home/banners/outdoor.jpg'),
            ],
            'category' => 'Design & Inspiration',
            'published_at' => now()->subHours(2),
            'views' => 348,
            'likes' => 24,
            'comments_count' => 8,
            'author' => [
                'name' => 'Studio Design',
                'type' => 'Interior Design Studio',
                'location' => 'Milan, Italy',
                'avatar' => asset('images/home/avatar-placeholder.jpg'),
                'website' => '#',
                'verified' => true,
                'followers' => 1240,
            ],
            'products' => [
                [
                    'id' => 101,
                    'name' => 'Modern Lounge Chair',
                    'category' => 'Hotel Furniture',
                    'price' => 'From $420',
                    'image' => asset('images/home/banners/hotel.jpg'),
                    'supplier' => 'Premium Furniture Co.',
                    'url' => '#',
                ],
                [
                    'id' => 102,
                    'name' => 'Outdoor Dining Collection',
                    'category' => 'Outdoor Furniture',
                    'price' => 'From $890',
                    'image' => asset('images/home/banners/outdoor.jpg'),
                    'supplier' => 'Outdoor Living Studio',
                    'url' => '#',
                ],
                [
                    'id' => 103,
                    'name' => 'Contemporary Hotel Seating',
                    'category' => 'Hospitality Seating',
                    'price' => 'From $350',
                    'image' => asset('images/home/banners/hero.jpg'),
                    'supplier' => 'Modern Contract Furniture',
                    'url' => '#',
                ],
            ],
        ];

       $comments = [
    [
        'author' => 'Alex Morgan',
        'avatar' => asset('images/333.JPG'),
        'date' => now()->subMinutes(35),
        'text' => 'Beautiful combination of natural materials and contemporary furniture. Really inspiring project!',
        'likes' => 4,
    ],
    [
        'author' => 'Emma Wilson',
        'avatar' => asset('images/444.JPG'),
        'date' => now()->subMinutes(58),
        'text' => 'I especially like the balance between functionality and aesthetics. Great work!',
        'likes' => 2,
    ],
    [
        'author' => 'Daniel Chen',
        'avatar' => asset('images/555.png'),
        'date' => now()->subHours(1),
        'text' => 'Could you share more details about the outdoor furniture collection?',
        'likes' => 1,
    ],
];

        return view('post.post', compact('post', 'comments'));
    }

    
    public function show(string $slug)
    {
        $locale = app()->getLocale();

        $post = $this->postService->getPublishedBySlug(
            slug: $slug,
            locale: $locale
        );

        abort_unless($post, 404);

        $translation = $this->postService->resolveTranslation(
            post: $post,
            locale: $locale
        );

        return view('community.show', compact(
            'post',
            'translation'
        ));
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $posts = $this->postService->search(
            locale: app()->getLocale(),
            search: $validated['q'] ?? '',
            perPage: 12
        );

        return view('community.search', [
            'posts' => $posts,
            'search' => $validated['q'] ?? '',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $this->ensureCanPublish();

        $categories = $this->categoryService->getActiveCategories();

        return view('community.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->ensureCanPublish();

        $validated = $request->validate($this->postRules());

        $identity = $this->context->identity();

        $post = $this->createPostAction->execute(
            userId: $identity['user_id'],
            categoryId: $validated['post_category_id'],
            translations: $validated['translations'],
            companyType: $identity['entity_type'],
            companyId: $identity['entity_id'],
            productIds: $validated['product_ids'] ?? [],
        );

        $this->uploadPostMedia(
            post: $post,
            files: $request->file('media', [])
        );

        return redirect()
            ->route('community.posts.edit', $post->id)
            ->with('success', __('community.post_created'));
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(int $id)
    {
        $post = $this->getManageablePost($id);

        $categories = $this->categoryService->getActiveCategories();

        $media = $this->getPostMedia($post);

        return view('community.edit', compact(
            'post',
            'categories',
            'media'
        ));
    }

    public function update(Request $request, int $id)
    {
        $post = $this->getManageablePost($id);

        $validated = $request->validate($this->postRules());

        $this->updatePostAction->execute(
            post: $post,
            categoryId: $validated['post_category_id'],
            translations: $validated['translations'],
            productIds: $validated['product_ids'] ?? [],
        );

        $this->deleteSelectedPostMedia(
            post: $post,
            mediaIds: $validated['remove_media'] ?? []
        );

        $this->uploadPostMedia(
            post: $post,
            files: $request->file('media', [])
        );

        return redirect()
            ->route('community.posts.edit', $post->id)
            ->with('success', __('community.post_updated'));
    }

    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    public function preview(int $id)
    {
        $this->getManageablePost($id);

        $identity = $this->context->identity();

        $post = $this->postService->getPreview(
            postId: $id,
            userId: $identity['user_id']
        );

        abort_unless($post, 404);

        $translation = $this->postService->resolveTranslation(
            post: $post,
            locale: app()->getLocale()
        );

        $media = $this->getPostMedia($post);

        return view('community.preview', compact(
            'post',
            'translation',
            'media'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | Post actions
    |--------------------------------------------------------------------------
    */

    public function submit(int $id)
    {
        $post = $this->getManageablePost($id);

        $this->submitPostForModerationAction->execute($post);

        return redirect()
            ->route('community.posts.edit', $post->id)
            ->with('success', __('community.post_submitted'));
    }

    public function archive(int $id)
    {
        $post = $this->getManageablePost($id);

        $this->archivePostAction->execute($post);

        return redirect()
            ->route('community.index')
            ->with('success', __('community.post_archived'));
    }

    public function destroy(int $id)
    {
        $post = $this->getManageablePost($id);

        $this->deleteAllPostMedia($post);

        $this->deletePostAction->execute($post);

        return redirect()
            ->route('community.index')
            ->with('success', __('community.post_deleted'));
    }

    


    /*
    |--------------------------------------------------------------------------
    | Active context
    |--------------------------------------------------------------------------
    */

    protected function ensureCanPublish(): void
    {
        $identity = $this->context->identity();

        abort_unless(
            !empty($identity['user_id'])
            && in_array(
                $identity['platform_role'] ?? null,
                ['buyer', 'supplier'],
                true
            )
            && !empty($identity['entity_type'])
            && !empty($identity['entity_id']),
            403
        );
    }

    protected function getManageablePost(int $id): Post
    {
        $this->ensureCanPublish();

        $identity = $this->context->identity();

        $post = $this->postService->getForEditing(
            postId: $id,
            userId: $identity['user_id']
        );

        abort_unless($post, 404);

        abort_unless(
            $post->company_type === $identity['entity_type']
            && (int) $post->company_id === (int) $identity['entity_id'],
            403
        );

        return $post;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    protected function postRules(): array
    {
        return [
            'post_category_id' => [
                'required',
                'integer',
                Rule::exists('post_categories', 'id')
                    ->where('is_active', true),
            ],

            'translations' => [
                'required',
                'array',
                'min:1',
            ],

            'translations.*.title' => [
                'required',
                'string',
                'max:255',
            ],

            'translations.*.slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],

            'translations.*.excerpt' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'translations.*.content' => [
                'required',
                'string',
            ],

            'translations.*.seo_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'translations.*.seo_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'product_ids' => [
                'nullable',
                'array',
            ],

            'product_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:products,id',
            ],

            'media' => [
                'nullable',
                'array',
                'max:10',
            ],

            'media.*' => [
                'required',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:10240',
            ],

            'remove_media' => [
                'nullable',
                'array',
            ],

            'remove_media.*' => [
                'required',
                'integer',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    protected function postMediaQuery(Post $post)
    {
        return Media::query()
            ->where('model_type', Post::class)
            ->where('model_id', $post->id)
            ->where('collection', 'community_posts');
    }

    protected function getPostMedia(Post $post)
    {
        return $this->postMediaQuery($post)
            ->orderBy('sort_order')
            ->get();
    }

    protected function uploadPostMedia(Post $post, array $files): void
    {
        if (empty($files)) {
            return;
        }

        $existingMedia = $this->postMediaQuery($post);

        $nextSortOrder = (int) $existingMedia->max('sort_order') + 1;

        $hasMainImage = $existingMedia->exists();

        foreach ($files as $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $metadata = $this->mediaService->extractMetadata($file);

            $dto = new UploadMediaDTO(
                file: $file,
                model: $post,
                collection: 'community_posts',
                mediaRole: 'image',
                private: false,
                originalFileName: $file->getClientOriginalName(),
                metadata: $metadata,
                sortOrder: $nextSortOrder,
                isMain: !$hasMainImage
            );

            $this->mediaService->upload($dto);

            $nextSortOrder++;
            $hasMainImage = true;
        }
    }

    protected function deleteSelectedPostMedia(
        Post $post,
        array $mediaIds
    ): void {
        if (empty($mediaIds)) {
            return;
        }

        $mediaItems = $this->postMediaQuery($post)
            ->whereIn('id', $mediaIds)
            ->get();

        foreach ($mediaItems as $media) {
            $this->mediaService->delete($media);
        }
    }

    protected function deleteAllPostMedia(Post $post): void
    {
        $mediaItems = $this->postMediaQuery($post)->get();

        foreach ($mediaItems as $media) {
            $this->mediaService->delete($media);
        }
    }
}