<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Domain\Product\Actions\ApplyProductEditingCopyAction;
use App\Domain\Product\Actions\DeleteProductEditingCopyAction;
use App\Domain\Returns\Models\ProductReturnPolicy;
use Illuminate\Validation\ValidationException;
use App\Domain\Product\Actions\DeleteProductAction;

use App\Models\User;
use App\Models\Country;
use App\Models\Language;
use App\Models\Location;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;



class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'newest');
        $supplierFilter = $request->get('supplier');

        $query = Product::query()
            ->with([
                'images',
                'supplier',
                'category',
                'priceTiers',
            ])
            ->where('status', 'pending');

        if ($supplierFilter) {
            $query->whereHas('supplier', function ($q) use ($supplierFilter) {
                $q->where('name', 'like', "%{$supplierFilter}%");
            });
        }

        match ($sort) {
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        $products = $query->paginate(20)->withQueryString();

        
        
        return view('dashboard.admin.products.index', compact(
            'products',
            'sort',
            'supplierFilter'
        ));
    }

    public function show(Product $product)
{
    $product->load([
        'images',
        'priceTiers',
        'supplier',
        'category.translations',
        'user',
        'translations',
        'materials.translations',
        'materials.photo',
        'materials.materialGroup',
        'shippingDimensions',
        'shippingTemplates.translations',
        'shippingTemplates.warehouse',
        'shippingTemplates.locations',
        'shippingTemplates.deliveryOptions.deliveryType',
        'paymentMethods.translations',
        'paymentTerms.translations',
        'attributeValues.attribute.translations',
        'attributeValues.attribute.group',
        'attributeValues.translations',
        'attributeValues.options.option.translations',
        'variantGroup.items.product.translations',
        'variantGroup.items.media',
        'variantPreview',
        'returnPolicy.returnPolicy.translations',
        'returnPolicy.returnPolicy.reasons.translations',
        'returnPolicy.returnPolicy.resolutions.translations',
    ]);

    $images = $product->images
        ->sortBy('sort_order')
        ->values();

    $publicDisk = Storage::disk('public');
    $imageCopies = [];

    $imageExtensions = [
        'jpg', 'jpeg', 'png', 'webp', 'gif',
        'avif', 'jfif', 'bmp', 'tif', 'tiff',
    ];

    foreach ($images as $image) {
        $filePath = ltrim(
            str_replace('\\', '/', (string) $image->file_path),
            '/'
        );

        $copies = collect();

        if ($filePath && substr_count($filePath, '/') >= 3) {
            $baseDirectory = dirname(dirname($filePath));

            try {
                $copyPaths = collect($publicDisk->allFiles($baseDirectory))
                    ->filter(function ($path) use ($imageExtensions) {
                        return in_array(
                            strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                            $imageExtensions,
                            true
                        );
                    })
                    ->values();

                $copies = $copyPaths->map(function ($copyPath) use (
                    $publicDisk,
                    $filePath,
                    $image
                ) {
                    $copyPath = str_replace('\\', '/', $copyPath);
                    $isOriginal = $copyPath === $filePath;

                    // Actual file size.
                    try {
                        $bytes = $publicDisk->size($copyPath);
                    } catch (\Throwable $e) {
                        $bytes = $isOriginal ? $image->size_bytes : null;
                    }

                    $bytes = is_numeric($bytes)
                        ? (int) $bytes
                        : null;

                    if ($bytes === null) {
                        $sizeLabel = 'Size unavailable';
                    } elseif ($bytes >= 1048576) {
                        $sizeLabel = number_format($bytes / 1048576, 2) . ' MB';
                    } elseif ($bytes >= 1024) {
                        $sizeLabel = number_format($bytes / 1024, 2) . ' KB';
                    } else {
                        $sizeLabel = number_format($bytes) . ' bytes';
                    }

                    // Read actual dimensions and MIME type of this specific file.
                    $absolutePath = $publicDisk->path($copyPath);

                    $imageInfo = is_file($absolutePath)
                        ? @getimagesize($absolutePath)
                        : false;

                    $extension = strtolower(
                        pathinfo($copyPath, PATHINFO_EXTENSION)
                    );

                    $mimeType = is_array($imageInfo)
                        ? ($imageInfo['mime'] ?? null)
                        : null;

                    if (!$mimeType) {
                        $mimeType = match ($extension) {
                            'jpg', 'jpeg', 'jfif' => 'image/jpeg',
                            'png' => 'image/png',
                            'webp' => 'image/webp',
                            'gif' => 'image/gif',
                            'avif' => 'image/avif',
                            'bmp' => 'image/bmp',
                            'tif', 'tiff' => 'image/tiff',
                            default => null,
                        };
                    }

                    $variantName = basename(dirname($copyPath));

                    return [
                        // Copy identification.
                        'label' => $isOriginal
                            ? 'Original'
                            : \Illuminate\Support\Str::headline($variantName),

                        'file_name' => basename($copyPath),
                        'path' => $copyPath,

                        // Use the current host and port.
                        'url' => '/storage/' . implode(
                            '/',
                            array_map('rawurlencode', explode('/', $copyPath))
                        ),

                        // Data specific to the selected file.
                        'size_bytes' => $bytes,
                        'size_label' => $sizeLabel,
                        'width' => is_array($imageInfo)
                            ? ($imageInfo[0] ?? null)
                            : ($isOriginal ? $image->width : null),
                        'height' => is_array($imageInfo)
                            ? ($imageInfo[1] ?? null)
                            : ($isOriginal ? $image->height : null),
                        'extension' => $extension,
                        'mime_type' => $mimeType,

                        // Metadata from the associated media record.
                        'media_id' => $image->id,
                        'uuid' => $image->uuid,
                        'original_file_name' => $image->original_file_name,
                        'collection' => $image->collection,
                        'media_role' => $image->media_role,
                        'file_path' => $image->file_path,
                        'checksum_hash' => $isOriginal
                            ? $image->checksum_hash
                            : null,
                        'processing_status' => $image->processing_status,
                        'sort_order' => $image->sort_order,
                        'views_count' => $image->views_count,
                        'unique_views_count' => $image->unique_views_count,
                        'model_type' => $image->model_type,
                        'model_id' => $image->model_id,
                        'created_at' => $image->created_at?->format('d M Y, H:i:s'),
                        'updated_at' => $image->updated_at?->format('d M Y, H:i:s'),
                        'deleted_at' => $image->deleted_at?->format('d M Y, H:i:s'),
                        'is_private' => (bool) $image->is_private,
                        'is_primary' => (bool) $image->is_primary,
                        'is_main' => (bool) $image->is_main,
                        'is_original' => $isOriginal,
                    ];
                })->sortByDesc('is_original')->values();
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Keep the original available if scanning the directory fails.
        if ($copies->isEmpty() && $image->cdn_url) {
            $bytes = is_numeric($image->size_bytes)
                ? (int) $image->size_bytes
                : null;

            $extension = strtolower(
                pathinfo($filePath, PATHINFO_EXTENSION)
            );

            $copies = collect([[
                'label' => 'Original',
                'file_name' => $image->file_name,
                'path' => $filePath,

                'url' => $filePath
                    ? '/storage/' . implode(
                        '/',
                        array_map('rawurlencode', explode('/', $filePath))
                    )
                    : $image->cdn_url,

                'size_bytes' => $bytes,
                'size_label' => $bytes === null
                    ? 'Size unavailable'
                    : ($bytes >= 1048576
                        ? number_format($bytes / 1048576, 2) . ' MB'
                        : ($bytes >= 1024
                            ? number_format($bytes / 1024, 2) . ' KB'
                            : number_format($bytes) . ' bytes')),

                'width' => $image->width,
                'height' => $image->height,
                'extension' => $extension,
                'mime_type' => $image->mime_type,

                'media_id' => $image->id,
                'uuid' => $image->uuid,
                'original_file_name' => $image->original_file_name,
                'collection' => $image->collection,
                'media_role' => $image->media_role,
                'file_path' => $image->file_path,
                'checksum_hash' => $image->checksum_hash,
                'processing_status' => $image->processing_status,
                'sort_order' => $image->sort_order,
                'views_count' => $image->views_count,
                'unique_views_count' => $image->unique_views_count,
                'model_type' => $image->model_type,
                'model_id' => $image->model_id,
                'created_at' => $image->created_at?->format('d M Y, H:i:s'),
                'updated_at' => $image->updated_at?->format('d M Y, H:i:s'),
                'deleted_at' => $image->deleted_at?->format('d M Y, H:i:s'),
                'is_private' => (bool) $image->is_private,
                'is_primary' => (bool) $image->is_primary,
                'is_main' => (bool) $image->is_main,
                'is_original' => true,
            ]]);
        }

        $imageCopies[$image->id] = $copies->all();
    }


    // =========================================================
// VARIANT IMAGE COPIES
// =========================================================
$variantImageCopies = [];

$variantItems = $product->variantGroup?->items ?? collect();

foreach ($variantItems as $variantItem) {
    $media = $variantItem->media;

    if (!$media) {
        continue;
    }

    $variantFilePath = ltrim(
        str_replace('\\', '/', (string) $media->file_path),
        '/'
    );

    $copyPaths = collect();

    if ($variantFilePath !== '') {
        $parentFolder = basename(dirname($variantFilePath));

        $renditionFolders = [
            'original',
            'large',
            'medium',
            'small',
            'thumb',
            'thumbnail',
        ];

        if (in_array($parentFolder, $renditionFolders, true)) {
            $baseDirectory = dirname(dirname($variantFilePath));
        } else {
            // Some variant images store their original directly in the collection.
            // Generated copies, when present, may live in a UUID-named directory.
            $baseDirectory = dirname($variantFilePath)
                . '/'
                . pathinfo($variantFilePath, PATHINFO_FILENAME);

            if (!is_dir($publicDisk->path($baseDirectory))) {
                $baseDirectory = null;
            }
        }

        try {
            if ($baseDirectory) {
                $copyPaths = collect($publicDisk->allFiles($baseDirectory));
            }

            // Include the original if it is stored directly in the collection.
            if ($publicDisk->exists($variantFilePath)) {
                $copyPaths->push($variantFilePath);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    $copyPaths = $copyPaths
        ->filter(fn ($path) => in_array(
            strtolower(pathinfo($path, PATHINFO_EXTENSION)),
            $imageExtensions,
            true
        ))
        ->unique()
        ->values();

    $copies = $copyPaths->map(function ($copyPath) use (
        $publicDisk,
        $variantFilePath,
        $media
    ) {
        $copyPath = str_replace('\\', '/', $copyPath);
        $isOriginal = $copyPath === $variantFilePath;
        $absolutePath = $publicDisk->path($copyPath);

        try {
            $bytes = (int) $publicDisk->size($copyPath);
        } catch (\Throwable $e) {
            $bytes = null;
        }

        if ($bytes === null) {
            $sizeLabel = 'Size unavailable';
        } elseif ($bytes >= 1048576) {
            $sizeLabel = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $sizeLabel = number_format($bytes / 1024, 2) . ' KB';
        } else {
            $sizeLabel = number_format($bytes) . ' bytes';
        }

        $imageInfo = is_file($absolutePath)
            ? @getimagesize($absolutePath)
            : false;

        $extension = strtolower(pathinfo($copyPath, PATHINFO_EXTENSION));

        $mimeType = is_array($imageInfo)
            ? ($imageInfo['mime'] ?? null)
            : null;

        if (!$mimeType) {
            $mimeType = match ($extension) {
                'jpg', 'jpeg', 'jfif' => 'image/jpeg',
                'png' => 'image/png',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                'avif' => 'image/avif',
                'bmp' => 'image/bmp',
                'tif', 'tiff' => 'image/tiff',
                default => null,
            };
        }

        return [
            'label' => $isOriginal
                ? 'Original'
                : \Illuminate\Support\Str::headline(basename(dirname($copyPath))),
            'file_name' => basename($copyPath),
            'path' => $copyPath,
            'url' => '/storage/' . implode(
                '/',
                array_map('rawurlencode', explode('/', $copyPath))
            ),
            'size_bytes' => $bytes,
            'size_label' => $sizeLabel,
            'width' => is_array($imageInfo) ? ($imageInfo[0] ?? null) : null,
            'height' => is_array($imageInfo) ? ($imageInfo[1] ?? null) : null,
            'extension' => $extension,
            'mime_type' => $mimeType,

            // Metadata from the associated media record.
            'media_id' => $media->id,
            'uuid' => $media->uuid,
            'original_file_name' => $media->original_file_name,
            'collection' => $media->collection,
            'media_role' => $media->media_role,
            'file_path' => $media->file_path,
            'checksum_hash' => $isOriginal ? $media->checksum_hash : null,
            'processing_status' => $media->processing_status,
            'sort_order' => $media->sort_order,
            'views_count' => $media->views_count,
            'unique_views_count' => $media->unique_views_count,
            'model_type' => $media->model_type,
            'model_id' => $media->model_id,
            'created_at' => $media->created_at?->format('d M Y, H:i:s'),
            'updated_at' => $media->updated_at?->format('d M Y, H:i:s'),
            'deleted_at' => $media->deleted_at?->format('d M Y, H:i:s'),
            'is_private' => (bool) $media->is_private,
            'is_primary' => (bool) $media->is_primary,
            'is_main' => (bool) $media->is_main,
            'is_original' => $isOriginal,
        ];
    })->sortByDesc('is_original')->values();

    // Fall back to the media record if its physical path cannot be scanned.
    if ($copies->isEmpty() && $media->cdn_url) {
        $copies = collect([[
            'label' => 'Original',
            'file_name' => $media->file_name,
            'path' => $variantFilePath,
            'url' => $variantFilePath
                ? '/storage/' . implode(
                    '/',
                    array_map('rawurlencode', explode('/', $variantFilePath))
                )
                : $media->cdn_url,
            'size_bytes' => is_numeric($media->size_bytes)
                ? (int) $media->size_bytes
                : null,
            'size_label' => is_numeric($media->size_bytes)
                ? number_format($media->size_bytes / 1024, 2) . ' KB'
                : 'Size unavailable',
            'width' => $media->width,
            'height' => $media->height,
            'extension' => strtolower(pathinfo(
                (string) $media->file_name,
                PATHINFO_EXTENSION
            )),
            'mime_type' => $media->mime_type,
            'media_id' => $media->id,
            'uuid' => $media->uuid,
            'original_file_name' => $media->original_file_name,
            'collection' => $media->collection,
            'media_role' => $media->media_role,
            'file_path' => $media->file_path,
            'checksum_hash' => $media->checksum_hash,
            'processing_status' => $media->processing_status,
            'sort_order' => $media->sort_order,
            'views_count' => $media->views_count,
            'unique_views_count' => $media->unique_views_count,
            'model_type' => $media->model_type,
            'model_id' => $media->model_id,
            'created_at' => $media->created_at?->format('d M Y, H:i:s'),
            'updated_at' => $media->updated_at?->format('d M Y, H:i:s'),
            'deleted_at' => $media->deleted_at?->format('d M Y, H:i:s'),
            'is_private' => (bool) $media->is_private,
            'is_primary' => (bool) $media->is_primary,
            'is_main' => (bool) $media->is_main,
            'is_original' => true,
        ]]);
    }

    $variantImageCopies[$media->id] = $copies->all();
}



// =========================================================
// MATERIAL IMAGE COPIES
// =========================================================
$materialImageCopies = [];

$renditionFolders = [
    'original', 'large', 'medium', 'small', 'thumb', 'thumbnail',
];

foreach ($product->materials as $material) {
    $media = $material->photo;

    if (!$media) {
        continue;
    }

    $filePath = ltrim(
        str_replace('\\', '/', (string) $media->file_path),
        '/'
    );

    $copies = collect();

    if ($filePath !== '') {
        $fileStem = pathinfo($filePath, PATHINFO_FILENAME);
        $parentFolder = basename(dirname($filePath));

        if (in_array($parentFolder, $renditionFolders, true)) {
            $baseDirectory = dirname(dirname($filePath));
        } else {
            $nestedDirectory = dirname($filePath) . '/' . $fileStem;

            $baseDirectory = is_dir($publicDisk->path($nestedDirectory))
                ? $nestedDirectory
                : dirname($filePath);
        }

        try {
            $copyPaths = collect();

            if (is_dir($publicDisk->path($baseDirectory))) {
                $copyPaths = collect($publicDisk->allFiles($baseDirectory));
            }

            if ($publicDisk->exists($filePath)) {
                $copyPaths->push($filePath);
            }

            $copyPaths = $copyPaths
                ->map(fn ($path) => str_replace('\\', '/', $path))
                ->filter(function ($path) use ($imageExtensions, $fileStem) {
                    return pathinfo($path, PATHINFO_FILENAME) === $fileStem
                        && in_array(
                            strtolower(pathinfo($path, PATHINFO_EXTENSION)),
                            $imageExtensions,
                            true
                        );
                })
                ->unique()
                ->values();

            $copies = $copyPaths->map(function ($copyPath) use (
                $publicDisk,
                $filePath,
                $media
            ) {
                $isOriginal = $copyPath === $filePath;
                $absolutePath = $publicDisk->path($copyPath);

                try {
                    $bytes = (int) $publicDisk->size($copyPath);
                } catch (\Throwable $e) {
                    $bytes = null;
                }

                if ($bytes === null) {
                    $sizeLabel = 'Size unavailable';
                } elseif ($bytes >= 1048576) {
                    $sizeLabel = number_format($bytes / 1048576, 2) . ' MB';
                } elseif ($bytes >= 1024) {
                    $sizeLabel = number_format($bytes / 1024, 2) . ' KB';
                } else {
                    $sizeLabel = number_format($bytes) . ' bytes';
                }

                $info = is_file($absolutePath)
                    ? @getimagesize($absolutePath)
                    : false;

                $extension = strtolower(
                    pathinfo($copyPath, PATHINFO_EXTENSION)
                );

                $mimeType = is_array($info)
                    ? ($info['mime'] ?? null)
                    : null;

                if (!$mimeType) {
                    $mimeType = match ($extension) {
                        'jpg', 'jpeg', 'jfif' => 'image/jpeg',
                        'png' => 'image/png',
                        'webp' => 'image/webp',
                        'gif' => 'image/gif',
                        'avif' => 'image/avif',
                        'bmp' => 'image/bmp',
                        'tif', 'tiff' => 'image/tiff',
                        default => null,
                    };
                }

                return [
                    'label' => $isOriginal
                        ? 'Original'
                        : \Illuminate\Support\Str::headline(
                            basename(dirname($copyPath))
                        ),
                    'file_name' => basename($copyPath),
                    'path' => $copyPath,
                    'url' => '/storage/' . implode(
                        '/',
                        array_map('rawurlencode', explode('/', $copyPath))
                    ),
                    'size_bytes' => $bytes,
                    'size_label' => $sizeLabel,
                    'width' => is_array($info) ? ($info[0] ?? null) : null,
                    'height' => is_array($info) ? ($info[1] ?? null) : null,
                    'extension' => $extension,
                    'mime_type' => $mimeType,

                    'media_id' => $media->id,
                    'uuid' => $media->uuid,
                    'original_file_name' => $media->original_file_name,
                    'collection' => $media->collection,
                    'media_role' => $media->media_role,
                    'file_path' => $media->file_path,
                    'checksum_hash' => $isOriginal
                        ? $media->checksum_hash
                        : null,
                    'processing_status' => $media->processing_status,
                    'sort_order' => $media->sort_order,
                    'views_count' => $media->views_count,
                    'unique_views_count' => $media->unique_views_count,
                    'model_type' => $media->model_type,
                    'model_id' => $media->model_id,
                    'created_at' => $media->created_at?->format('d M Y, H:i:s'),
                    'updated_at' => $media->updated_at?->format('d M Y, H:i:s'),
                    'deleted_at' => $media->deleted_at?->format('d M Y, H:i:s'),
                    'is_private' => (bool) $media->is_private,
                    'is_primary' => (bool) $media->is_primary,
                    'is_main' => (bool) $media->is_main,
                    'is_original' => $isOriginal,
                ];
            })->sortByDesc('is_original')->values();
        } catch (\Throwable $e) {
            report($e);
        }
    }

    if ($copies->isEmpty() && $media->cdn_url) {
        $extension = strtolower(
            pathinfo($filePath, PATHINFO_EXTENSION)
        );

        $bytes = is_numeric($media->size_bytes)
            ? (int) $media->size_bytes
            : null;

        $copies = collect([[
            'label' => 'Original',
            'file_name' => $media->file_name,
            'path' => $filePath,
            'url' => $filePath
                ? '/storage/' . implode(
                    '/',
                    array_map('rawurlencode', explode('/', $filePath))
                )
                : $media->cdn_url,
            'size_bytes' => $bytes,
            'size_label' => $bytes === null
                ? 'Size unavailable'
                : number_format($bytes / 1024, 2) . ' KB',
            'width' => $media->width,
            'height' => $media->height,
            'extension' => $extension,
            'mime_type' => $media->mime_type,
            'media_id' => $media->id,
            'uuid' => $media->uuid,
            'original_file_name' => $media->original_file_name,
            'collection' => $media->collection,
            'media_role' => $media->media_role,
            'file_path' => $media->file_path,
            'checksum_hash' => $media->checksum_hash,
            'processing_status' => $media->processing_status,
            'sort_order' => $media->sort_order,
            'views_count' => $media->views_count,
            'unique_views_count' => $media->unique_views_count,
            'model_type' => $media->model_type,
            'model_id' => $media->model_id,
            'created_at' => $media->created_at?->format('d M Y, H:i:s'),
            'updated_at' => $media->updated_at?->format('d M Y, H:i:s'),
            'deleted_at' => $media->deleted_at?->format('d M Y, H:i:s'),
            'is_private' => (bool) $media->is_private,
            'is_primary' => (bool) $media->is_primary,
            'is_main' => (bool) $media->is_main,
            'is_original' => true,
        ]]);
    }

    $materialImageCopies[$media->id] = $copies->all();
}


    $mainImage = $images->firstWhere('is_main', true)
        ?? $images->firstWhere('is_primary', true)
        ?? $images->first();

    $creator = $product->created_by
        ? User::find($product->created_by)
        : null;

    $supplierLanguage = $product->supplier?->language
        ? Language::where('code', $product->supplier->language)->first()
        : null;

    $originCountry = $product->country_id
        ? Country::withCurrentTranslation()->whereKey($product->country_id)->first()
        : null;

    $originRegion = $product->origin_region_id
        ? Location::find($product->origin_region_id)
        : null;

    $originCity = $product->origin_city_id
        ? Location::find($product->origin_city_id)
        : null;

    // Product return settings
$productReturnPolicy = $product->returnPolicy;

// Resolve the supplier's default policy only when the product uses it.
$supplierDefaultReturnPolicyId = null;

if ($productReturnPolicy?->use_supplier_default) {
    $supplierId = $product->supplier_id
        ?? $product->supplier?->getKey();

    if ($supplierId) {
        $supplierDefaultReturnPolicyId =
            \App\Domain\Returns\Models\SupplierReturnPolicySetting::query()
                ->where('supplier_id', $supplierId)
                ->value('default_return_policy_id');
    }
}

// Resolve only the policy applicable to this product.
if ($productReturnPolicy?->use_supplier_default) {
    $effectiveReturnPolicy = $supplierDefaultReturnPolicyId
        ? \App\Domain\Returns\Models\ReturnPolicy::query()
            ->with([
                'translations',
                'reasons.translations',
                'resolutions.translations',
            ])
            ->find($supplierDefaultReturnPolicyId)
        : null;
} else {
    $effectiveReturnPolicy = $productReturnPolicy?->returnPolicy;
}

    $originalProduct = $product->original_product_id
        ? Product::with('translations')->find($product->original_product_id)
        : null;

    $languagesSortOrder = Language::query()->pluck('sort_order', 'code');

    $translations = $product->translations
        ->sortBy(fn ($translation) => $languagesSortOrder[$translation->locale] ?? PHP_INT_MAX)
        ->values();

    return view('dashboard.admin.products.show', [
        'product1' => $product,
        'translations' => $translations,
        'images' => $images,
        'imageCopies' => $imageCopies,
        'variantImageCopies' => $variantImageCopies,
        'materialImageCopies' => $materialImageCopies,
        'mainImage' => $mainImage,
        'creator' => $creator,
        'supplierLanguage' => $supplierLanguage,
        'originCountry' => $originCountry,
        'originRegion' => $originRegion,
        'originCity' => $originCity,
        'productReturnPolicy' => $productReturnPolicy,
        'originalProduct' => $originalProduct,
        'effectiveReturnPolicy' => $effectiveReturnPolicy,
    ]);
}
    public function approve(
    Product $product,
    ApplyProductEditingCopyAction $applyEditingCopyAction,
    DeleteProductEditingCopyAction $deleteEditingCopyAction
) {
    try {
        if ($product->original_product_id !== null) {
            $original = $applyEditingCopyAction->execute($product);

            $deleteEditingCopyAction->execute($product);

            return redirect()
                ->route('admin.products.index')
                ->with(
                    'success',
                    "Changes to product #{$original->id} approved successfully."
                );
        }

        if ($product->status !== 'pending') {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'Only pending products can be approved.');
        }

        $product->update([
            'status' => 'approved',
            'reject_reason' => null,
        ]);

        return redirect()
            ->route('admin.products.index')
            ->with(
                'success',
                "Product #{$product->id} approved successfully."
            );
    } catch (ValidationException $e) {
        throw $e;
    }
}

    public function reject(Request $request, Product $product)
    {
        $request->validate([
            'reject_reason' => 'required|string|max:1000',
        ]);

        $product->update([
            'status' => 'rejected',
            'reject_reason' => $request->input('reject_reason'),
        ]);

        return back()->with('error', 'Product rejected with reason.');
    }


    public function destroy(
    Product $product,
    DeleteProductAction $deleteProductAction
) {
    try {
        $productId = $product->id;

        $deleteProductAction->execute($product);

        return redirect()
            ->route('admin.products.management')
            ->with(
                'success',
                "Product #{$productId} deleted successfully."
            );
    } catch (ValidationException $e) {
        throw $e;
    }
}

public function managementIndex()
{
    $sort = request('sort', '');
    $status = request('status', '');
    $userFilter = request('user', '');

    $products = Product::query()
        ->with(['supplier.user', 'images'])
        ->whereNull('original_product_id')
        ->when(
            in_array($status, ['pending', 'approved', 'rejected'], true),
            fn ($query) => $query->where('status', $status)
        )
        ->when(
            $userFilter !== '',
            fn ($query) => $query->whereHas('supplier.user', function ($query) use ($userFilter) {
                $query->where('name', 'like', "%{$userFilter}%")
                    ->orWhere('last_name', 'like', "%{$userFilter}%");
            })
        )
        ->when(
            $sort === 'oldest',
            fn ($query) => $query->orderBy('created_at'),
            fn ($query) => $query->orderByDesc('created_at')
        )
        ->paginate(20)
        ->withQueryString();

    return view('dashboard.admin.products.management', compact(
        'products',
        'sort',
        'status',
        'userFilter'
    ));
}

}