<?php


//Generate react color
function yncreaction_color_title($aItem)
{
    if (empty($aItem['title'])) {
        return '';
    }
    return '<strong style="color: #' . $aItem['color'] . '" class="ync-reaction-title">' . _p($aItem['title']) . '</strong>';
}

$module = Phpfox_Module::instance();

$module->addAliasNames('yncreaction', 'YNC_Reaction')
    ->addComponentNames('controller', [
        'yncreaction.admincp.manage-reactions' => Apps\YNC_Reaction\Controller\Admin\ManageReactionsController::class,
        'yncreaction.admincp.add-reaction' => Apps\YNC_Reaction\Controller\Admin\AddReactionController::class
    ])
    ->addComponentNames('block', [
        'yncreaction.reaction-link' => Apps\YNC_Reaction\Block\ReactionLinkBlock::class,
        'yncreaction.reaction-display' => Apps\YNC_Reaction\Block\ReactionDisplayBlock::class,
        'yncreaction.reaction-list-mini' => Apps\YNC_Reaction\Block\ReactionListMiniBlock::class,
        'yncreaction.list-react-by-item' => Apps\YNC_Reaction\Block\ListReactByItemBlock::class,
        'yncreaction.detail-react' => Apps\YNC_Reaction\Block\DetailReactBlock::class,
        'yncreaction.user-row' => Apps\YNC_Reaction\Block\UserRowBlock::class
    ])
    ->addComponentNames('ajax', [
        'yncreaction.ajax' => Apps\YNC_Reaction\Ajax\Ajax::class
    ])
    ->addServiceNames([
        'yncreaction' => Apps\YNC_Reaction\Service\Yncreaction::class,
        'yncreaction.react' => Apps\YNC_Reaction\Service\React::class,
        'yncreaction.process' => Apps\YNC_Reaction\Service\Process::class
    ])
    ->addTemplateDirs([
        'yncreaction' => PHPFOX_DIR_SITE_APPS . 'ync-reaction' . PHPFOX_DS . 'views',
    ]);

group('/admincp', function () {
    route('/yncreaction', function () {
        auth()->isAdmin(true);
        Phpfox::getLib('module')->dispatch('yncreaction.admincp.manage-reactions');
        return 'controller';
    });
    route('/yncreaction/reactions-order', function () {
        auth()->isAdmin(true);
        $ids = request()->get('ids');
        $ids = trim($ids, ',');
        $ids = explode(',', $ids);
        $values = [];
        foreach ($ids as $key => $id) {
            $values[$id] = $key + 1;
        }
        Phpfox::getService('core.process')->updateOrdering([
                'table' => 'yncreaction_reactions',
                'key' => 'id',
                'values' => $values,
            ]
        );
        Phpfox::getLib('cache')->removeGroup('yncreaction');
        return true;
    });
});