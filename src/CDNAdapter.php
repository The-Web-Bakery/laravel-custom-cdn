<?php

namespace TheWebbakery\CDN;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use League\Flysystem\ChecksumAlgoIsNotSupported;
use League\Flysystem\ChecksumProvider;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FilesystemException;
use League\Flysystem\InvalidVisibilityProvided;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToCheckExistence;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToProvideChecksum;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use TheWebbakery\CDN\CDNClient as Client;
use TheWebbakery\CDN\Resources\FileResource;

class CDNAdapter implements FilesystemAdapter, ChecksumProvider {

    private Client $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     */
    public function fileExists(string $path): bool
    {
        return !is_null($this->client->files()->find($path));
    }

    /**
     * @throws FilesystemException
     * @throws UnableToCheckExistence
     */
    public function directoryExists(string $path): bool
    {
        return !is_null($this->client->folders()->all(path: $path));
    }

    /**
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function write(string $path, $contents, Config $config): void
    {
        if($filename = $config->get('filename')) {
            $path = str_replace($filename, '', $path);
        }
        $this->client->files()->upload(
            file: $contents,
            filename: $filename ?? null,
            path: $path,
        );
    }

    /**
     * @param resource $contents
     *
     * @throws UnableToWriteFile
     * @throws FilesystemException
     */
    public function writeStream(string $path, $contents, Config $config): void
    {
        $this->write($path, $contents, $config);
    }

    /**
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function read(string $path): string
    {
        $file = $this->client->files()->find($path);

        if(is_null($file)) {
            throw new FileNotFoundException($path);
        }

        return $file->getContent();
    }

    /**
     * @return resource
     *
     * @throws UnableToReadFile
     * @throws FilesystemException
     */
    public function readStream(string $path)
    {
        return $this->read($path);
    }

    /**
     * @throws UnableToDeleteFile
     * @throws FilesystemException
     */
    public function delete(string $path): void
    {
        $this->client->files()->delete($path);
    }

    /**
     * @throws UnableToDeleteDirectory
     * @throws FilesystemException
     */
    public function deleteDirectory(string $path): void
    {
        dd(
            "TODO"
        );
    }

    /**
     * @throws UnableToCreateDirectory
     * @throws FilesystemException
     */
    public function createDirectory(string $path, Config $config): void
    {
        $filename = $config->get('filename') ?: last(explode('/', $path));
        $this->client->folders()->create(
            name: $filename,
            path: $path,
        );
    }

    /**
     * @throws InvalidVisibilityProvided
     * @throws FilesystemException
     */
    public function setVisibility(string $path, string $visibility): void
    {
        // TODO: Implement setVisibility() method.
    }

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function visibility(string $path): FileAttributes
    {
        // TODO: Implement visibility() method.
    }

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function mimeType(string $path): FileAttributes
    {
        // TODO: Implement mimeType() method.
    }

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function lastModified(string $path): FileAttributes
    {
        // TODO: Implement lastModified() method.
    }

    /**
     * @throws UnableToRetrieveMetadata
     * @throws FilesystemException
     */
    public function fileSize(string $path): FileAttributes
    {
        $response = $this->client->files()->find($path);

        return new FileAttributes(
            $path,
            $response->details['size'] ?? null
        );
    }

    /**
     * @return iterable<StorageAttributes>
     *
     * @throws FilesystemException
     */
    public function listContents(string $path, bool $deep): iterable
    {
//        if(!str_starts_with($path, '/')) {
//            $path = sprintf('/%s', $path);
//        }

        $list = $this->client->folders()->all($path, recursive: $deep);

        foreach ($list->files as $item) {
            yield $this->normalizeObject($item);
        }
    }

    /**
     * @param array $item Files and folders from listContents.
     *
     * @return StorageAttributes
     */
    protected function normalizeObject(object $item): StorageAttributes
    {
        return match ($item->type) {

            'folder' => new DirectoryAttributes(
                $item->folderPath,
                null,
                strtotime($item->updatedAt),
                ['id'   => $item->folderId]
            ),

            'file' => new FileAttributes(
                $item->filePath,
                $item->size,
                null,
                strtotime($item->updatedAt),
                $item->mime ?? null,
                ['id'   => $item->fileId]
            )

        };
    }

    /**
     * @throws UnableToMoveFile
     * @throws FilesystemException
     */
    public function move(string $source, string $destination, Config $config): void
    {
        dd(
            "TODO"
        );
    }

    /**
     * @throws UnableToCopyFile
     * @throws FilesystemException
     */
    public function copy(string $source, string $destination, Config $config): void
    {
        dd(
            "TODO"
        );
    }

    /**
     * @return string MD5 hash of the file contents
     *
     * @throws UnableToProvideChecksum
     * @throws ChecksumAlgoIsNotSupported
     */
    public function checksum(string $path, Config $config): string
    {
        dd(
            "TODO"
        );
    }

    public function getUrl(string $path): ?string
    {
        return $this->client->files()->find($path)?->url;
    }
}