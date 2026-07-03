<?php

namespace App\Services;

use App\DTOs\CreateDocumentationData;
use App\Models\DocumentationPhoto;
use App\Models\FieldDocumentation;
use App\Models\MediaFile;
use App\Repositories\DocumentationRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DocumentationService
{
    public function __construct(
        private readonly DocumentationRepositoryInterface $documentationRepository,
    ) {}

    public function createDocumentation(CreateDocumentationData $data): FieldDocumentation
    {
        return $this->documentationRepository->create($data->toArray());
    }

    public function paginate(?string $employeeId, int $perPage = 10, array $filters = []): LengthAwarePaginator
    {
        return $this->documentationRepository->paginate($employeeId, $perPage, $filters);
    }

    public function getDocumentation(string $id): ?FieldDocumentation
    {
        return $this->documentationRepository->findById($id);
    }

    public function deleteDocumentation(string $id): void
    {
        $documentation = $this->documentationRepository->findById($id);

        if (!$documentation) {
            throw new \RuntimeException('Documentation not found');
        }

        $this->documentationRepository->delete($documentation);
    }

    public function uploadPhoto(string $documentationId, string $mediaFileId, int $sortOrder = 0): DocumentationPhoto
    {
        $documentation = $this->documentationRepository->findById($documentationId);

        if (!$documentation) {
            throw new \RuntimeException('Documentation not found');
        }

        return DocumentationPhoto::create([
            'documentation_id' => $documentationId,
            'media_file_id' => $mediaFileId,
            'sort_order' => $sortOrder,
        ]);
    }
}
