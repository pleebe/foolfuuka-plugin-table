<?php

namespace Foolz\FoolFuuka\Controller\Chan;

use Foolz\FoolFrame\Model\Plugins;
use Foolz\FoolFrame\Model\Uri;
use Foolz\FoolFuuka\Model\Board;
use Foolz\FoolFuuka\Model\Comment;
use Foolz\Plugin\Plugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Table extends \Foolz\FoolFuuka\Controller\Chan
{
    /**
     * @var Plugin
     */
    protected $plugin;

    /**
     * @var Uri
     */
    protected $uri;

    public function before()
    {
        $this->plugin = $this->getContext()->getService('plugins')->getPlugin('foolz/foolfuuka-plugin-table');
        $this->uri = $this->getContext()->getService('uri');

        parent::before();
    }

    public function radix_page($page = 1)
    {
        try {
            $board = Board::forge($this->getContext())
                ->getThreads()
                ->setRadix($this->radix)
                ->setPage($page)
                ->setOptions('per_page', 100);

            $count = $board->getCount();

            $board = $board->getComments();
            $radix = $this->radix;

            ob_start();
            ?>

            <link href="<?= $this->plugin->getAssetManager()->getAssetLink('style.css') ?>" rel="stylesheet"
                  type="text/css"/>

            <?php
            include __DIR__ . '/../../views/Table.php';

            $string = ob_get_clean();
            $partial = $this->builder->createPartial('body', 'plugin');
            $partial->getParamManager()->setParam('content', $string);

            $this->param_manager->setParams([
                'pagination' => [
                    'base_url' => $this->uri->create([$this->radix->shortname, 'table', 'page']),
                    'current_page' => $page,
                    'total' => $count/100
                ]
            ]);
        } catch (\Foolz\Foolfuuka\Model\BoardException $e) {
            return $this->error($e->getMessage());
        }

        return new Response($this->builder->build());
    }
}
