<?php

namespace AppBundle\Controller;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\LocationService;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\MVC\Symfony\Controller\Controller;
use eZ\Publish\Core\Pagination\Pagerfanta\LocationSearchAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class HomePageController extends Controller
{
    private $contentService;

    private $locationService;

    public function __construct(ContentService $contentService, LocationService $locationService)
    {
        $this->contentService = $contentService;
        $this->locationService = $locationService;
    }

    public function getAllRidesAction(Request $request, Location $location)
    {
        $rootLocationId = $this->getConfigResolver()->getParameter('content.tree_root.location_id');
        $rootLocation = $this->locationService->loadLocation($rootLocationId);

        return $this->render(
            'list/rides.html.twig',
            [
                'pagerRides' => $this->findRides($rootLocation, $request),
                'location' => $location
            ]
        );
    }

    private function findRides(Location $rootLocation, Request $request)
    {
        $query = new LocationQuery();

        $query->query = new Criterion\LogicalAnd(
            [
                new Criterion\Visibility(Criterion\Visibility::VISIBLE),
                new Criterion\ContentTypeIdentifier(['ride']),
            ]
        );
        $query->sortClauses = [
            new SortClause\DatePublished(LocationQuery::SORT_ASC),
        ];

        $pager = new Pagerfanta(
            new LocationSearchAdapter($query, $this->getRepository()->getSearchService())
        );
        $pager->setMaxPerPage(10);
        $pager->setCurrentPage($request->get('page', 1));

        return $pager;
    }
}