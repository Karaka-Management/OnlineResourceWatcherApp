<?php

declare(strict_types=1);

namespace Controllers;

use Models\AuditMapper;
use phpOMS\Contract\RenderableInterface;
use phpOMS\DataStorage\Database\Query\OrderType;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;
use phpOMS\Utils\Parser\Markdown\Markdown;
use Views\TableView;
use WebApplication;

class BackendController
{
    private WebApplication $app;

    public function __construct(WebApplication $app = null)
    {
        $this->app = $app;
    }

	public function signinView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Backend/tpl/signin');

		return $view;
	}

	public function dashboardView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null): RenderableInterface
	{
		$view = new View($this->app->l11nManager, $request, $response);
		$view->setTemplate('/Applications/Backend/tpl/user-dashboard');

		return $view;
	}

    public function adminOrganizationsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/admin-organizations');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function adminUsersView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/admin-users');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function adminResourcesView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/admin-resources');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function adminBillsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/admin-bills');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function adminLogsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/admin-logs');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function organizationSettingsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/organization-settings');

        return $view;
    }

    public function organizationUsersEditView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/organization-users-edit');

        return $view;
    }

    public function organizationUsersView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/organization-users');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function organizationResourcesView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/organization-resources');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function organizationBillsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/organization-bills');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }


    public function userSettingsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/user-settings');

        return $view;
    }

    public function userResourcesCreateView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/user-resources-create');

        return $view;
    }

    public function userResourcesView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/user-resources');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

    public function userReportsView(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface {
        $view = new View($this->app->l11nManager, $request, $response);
        $view->setTemplate('/Applications/Backend/tpl/user-reports');

        /* Table functionality */

        $searchFieldData = $request->getLike('.*\-p\-.*');
        $searchField     = [];
        foreach ($searchFieldData as $key => $data) {
            if ($data === '1') {
                $split  = \explode('-', $key);
                $member =  \end($split);

                $searchField[] = $member;
            }
        }

        $filterFieldData = $request->getLike('.*\-f\-.*?\-t');
        $filterField     = [];
        foreach ($filterFieldData as $key => $type) {
            $split = \explode('-', $key);
            \end($split);

            $member = \prev($split);

            if (!empty($request->getData('auditlist-f-' . $member . '-f1'))) {
                $filterField[$member] = [
                    'type'   => $type,
                    'value1' => $request->getData('auditlist-f-' . $member . '-f1'),
                    'logic1' => $request->getData('auditlist-f-' . $member . '-o1'),
                    'value2' => $request->getData('auditlist-f-' . $member . '-f2'),
                    'logic2' => $request->getData('auditlist-f-' . $member . '-o2'),
                ];
            }
        }

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = AuditMapper::getAll()->with('createdBy');
        $list   = AuditMapper::find(
            search: $request->getData('search'),
            mapper: $mapper,
            id: (int) ($request->getData('id') ?? 0),
            secondaryId: (string) ($request->getData('subid') ?? ''),
            type: $request->getData('pType'),
            pageLimit: empty((int) ($request->getData('limit') ?? 0)) ? 100 : ((int) $request->getData('limit')),
            sortBy: $request->getData('sort_by') ?? '',
            sortOrder: $request->getData('sort_order') ?? OrderType::DESC,
            searchFields: $searchField,
            filters: $filterField
        );

        $view->setData('audits', $list['data']);

        $tableView         = new TableView($this->app->l11nManager, $request, $response);
        $tableView->module = 'Auditor';
        $tableView->theme  = 'Backend';
        $tableView->setTitleTemplate('/Templates/table-title');
        $tableView->setColumnHeaderElementTemplate('/Templates/header-element-table');
        $tableView->setFilterTemplate('/Templates/popup-filter-table');
        $tableView->setSortTemplate('/Templates/sort-table');
        $tableView->setData('hasPrevious', $list['hasPrevious']);
        $tableView->setData('hasNext', $list['hasNext']);

        $view->addData('tableView', $tableView);

        return $view;
    }

}
