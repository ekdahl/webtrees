<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2021 webtrees development team
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Fisharebest\Webtrees\Http\RequestHandlers;

use Fisharebest\Webtrees\Services\MediaFileService;
use Fisharebest\Webtrees\Services\SearchService;
use Fisharebest\Webtrees\Tree;
use Illuminate\Support\Collection;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\ServerRequestInterface;

use function assert;

/**
 * Autocomplete handler for media folders
 */
class AutoCompleteFolder extends AbstractAutocompleteHandler
{
    private MediaFileService $media_file_service;

    public function __construct(MediaFileService $media_file_service, SearchService $search_service)
    {
        parent::__construct($search_service);

        $this->media_file_service = $media_file_service;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return Collection<int,string>
     */
    protected function search(ServerRequestInterface $request): Collection
    {
        $tree = $request->getAttribute('tree');
        assert($tree instanceof Tree);

        $query = $request->getQueryParams()['query'] ?? '';

        try {
            return $this->media_file_service->mediaFolders($tree)
                ->filter(fn (string $path): bool => stripos($path, $query) !== false);
        } catch (FilesystemException $ex) {
            return new Collection();
        }
    }
}
