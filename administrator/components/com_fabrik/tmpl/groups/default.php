<?php
/**
 * Admin Group List Tmpl
 *
 * @package     Joomla.Administrator
 * @subpackage  Fabrik
 * @copyright   Copyright (C) 2005-2016  Media A-Team, Inc. - All rights reserved.
 * @license     GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * @since       3.0
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('script', 'system/multiselect.js', false, true);

$user = Factory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');

?>
<form action="<?php echo JRoute::_('index.php?option=com_fabrik&view=groups'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div id="j-sidebar-container" class="col-md-2">
			<?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="col-md-10">
			<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>

        <div class="clearfix"></div>
        <table class="table table-striped">
            <thead>
            <tr>
                <th width="2%">
                    <?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 'g.id', $listDirn, $listOrder); ?>
                </th>
                <th width="1%">
                    <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this)" />
                </th>
                <th width="30%" >
                    <?php echo HTMLHelper::_('grid.sort', 'COM_FABRIK_NAME', 'g.name', $listDirn, $listOrder); ?>
                </th>
                <th width="30%" >
                    <?php echo HTMLHelper::_('grid.sort', 'COM_FABRIK_LABEL', 'g.label', $listDirn, $listOrder); ?>
                </th>
                <th width="30%">
                    <?php echo HTMLHelper::_('grid.sort', 'COM_FABRIK_FORM', 'f.label', $listDirn, $listOrder); ?>
                </th>
                <th width="31%">
                    <?php echo Text::_('COM_FABRIK_ELEMENTS'); ?>
                </th>
                <th width="5%">
                    <?php echo HTMLHelper::_('grid.sort', 'JPUBLISHED', 'g.published', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="7">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
            <?php foreach ($this->items as $i => $item) :
                $ordering = ($listOrder == 'ordering');
                $link = JRoute::_('index.php?option=com_fabrik&task=group.edit&id=' . (int) $item->id);
                $canCreate = $user->authorise('core.create', 'com_fabrik.group.' . $item->form_id);
                $canEdit = $user->authorise('core.edit', 'com_fabrik.group.' . $item->form_id);
                $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
                $canChange = $user->authorise('core.edit.state', 'com_fabrik.group.' . $item->form_id) && $canCheckin;
                ?>

                <tr class="row<?php echo $i % 2; ?>">
                    <td>
                        <?php echo $item->id; ?>
                    </td>
                    <td>
                        <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                    </td>
                    <td>
                        <?php if ($item->checked_out) : ?>
                            <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'groups.', $canCheckin); ?>
                        <?php endif; ?>
                        <?php
                        if ($item->checked_out && ($item->checked_out != $user->get('id'))) :
                            echo $item->name;
                        else :
                            ?>
                            <a href="<?php echo $link; ?>">
                                <?php echo $item->name; ?>
                            </a>
                        <?php endif; ?>
                    <td>
                        <?php echo $item->label; ?>
                    </td>
                    </td>
                    <td>
                        <a href="index.php?option=com_fabrik&task=form.edit&id=<?php echo $item->form_id?>">
                            <i class="icon-pencil"></i> <?php echo $item->flabel; ?>
                        </a>
                    </td>
                    <td>
                        <a href="index.php?option=com_fabrik&view=element&layout=edit&filter_groupId=<?php echo $item->id ?>">
                            <i class="icon-plus"></i>
                            <?php echo Text::_('COM_FABRIK_ADD')?>
                        </a>
                        <span class="badge badge-info"><?php echo $item->_elementCount; ?></span>
                    </td>
                    <td class="center">
                        <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'groups.', $canChange); ?>
                    </td>
                </tr>

            <?php endforeach; ?>
            </tbody>
        </table>

        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
