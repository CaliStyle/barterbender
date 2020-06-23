<?php
defined('PHPFOX') or exit('NO DICE!');
?>

<form id="ynecommerce_requests_search" action="{url link=$sModule}" method="GET">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                {phrase var='search_filter'}
            </div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="form-group col-md-6">
                    <label>{phrase var='request_by'}:</label>
                    <input title="{phrase var='request_by'}" id="requester_name" value="{value type='input' id='requester_name'}" type="text" name="search[requester_name]" class="form-control requester_name">
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='status'}:</label>
                    <select title="{phrase var='status'}" class="form-control" id="request_status" name="search[request_status]">
                        <option value="all" {value type='select' id='request_status' default='all' }>{phrase var='all'}</option>
                        <option value="pending" {value type='select' id='request_status' default='pending' }>{phrase var='pending'}</option>
                        <option value="approved" {value type='select' id='request_status' default='approved' }>{phrase var='ecommerce.approved'}</option>
                        <option value="rejected" {value type='select' id='request_status' default='rejected' }>{phrase var='ecommerce.rejected'}</option>
                    </select>
                </div>

                <div class="form-group col-md-6">
                    <label>{phrase var='from_date'}:</label>
                    {select_date prefix='from_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' default_all=true}
                </div>
                <div class="form-group col-md-6">
                    <label>{phrase var='to_date'}:</label>
                    {select_date prefix='to_' id='_start' start_year='current_year' end_year='+1' field_separator=' / ' default_all=true}
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <input type="submit" id="btn_search" name="search[submit]" value="{phrase var='search'}" class="btn btn-primary">
            <input type="submit" name="reset" value="{phrase var='reset'}" class="btn btn-danger">
        </div>
    </div>
</form>

{if count($aRequestRows)}
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">
            {phrase var='manage_requests'}
        </div>
    </div>
    <div class="table-responsive">
        <table align='center' class="ynecommerce_full_table table table-striped table-bordered">
            <thead>
            <tr>
                <th class="request_date ynauction-paddingright">
                    {phrase var='request_date'}
                    <div class="ynauction_column_sort_up_down">
                        <a href="{$sCustomBaseLink}sortfield_request_date/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_request_date/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="requester ynauction-paddingright">
                    {phrase var='requester'}
                    <div class="ynauction_column_sort_up_down">
                        <a href="{$sCustomBaseLink}sortfield_requester/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_requester/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="amount ynauction-paddingright">
                    {phrase var='amount'}
                    <div class="ynauction_column_sort_up_down">
                        <a href="{$sCustomBaseLink}sortfield_amount/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_amount/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="status ynauction-paddingright">
                    {phrase var='status'}
                    <div class="ynauction_column_sort_up_down">
                        <a href="{$sCustomBaseLink}sortfield_status/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_status/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="request_message ynauction-paddingright">
                    {phrase var='request_message'}
                </th>
                <th class="response_date ynauction-paddingright">
                    {phrase var='response_date'}
                    <div class="ynauction_column_sort_up_down">
                        <a href="{$sCustomBaseLink}sortfield_response_date/sorttype_asc/" class="up"></a>
                        <a href="{$sCustomBaseLink}sortfield_response_date/sorttype_desc/" class="down"></a>
                    </div>
                </th>
                <th class="order_total price">
                    {phrase var='response_message'}
                </th>
                <th class="options">{phrase var='actions'}</th>
            </tr>
            </thead>
            <tbody>
            {foreach from=$aRequestRows item=aRequestRow}
            {php}
            $aRequestRow = $this->_aVars['aRequestRow'];
            {/php}
            <tr>
                <td><?php echo Phpfox::getTime('d/m/Y',$aRequestRow['creditmoneyrequest_creation_datetime']) ;?></td>
                <td class="buyer">
                    <?php
                    $aRequester = Phpfox::getService('user')->get($aRequestRow['user_id']);
                    if($aRequester):
                        ?>
                        <?php
                        $aRequesterLink = Phpfox::getService('user')->getLink($aRequester['user_id'], $aRequester['user_name']);
                        ;?>
                        <a style="text-decoration: none;" href="<?php echo $aRequesterLink;?>"><?php echo $aRequester['full_name'];?></a>
                    <?php endif;?>
                </td>
                <td>{$aRequestRow.creditmoneyrequest_amount|number_format:2}$</td>
                <td><?php echo _p(''.$aRequestRow['creditmoneyrequest_status']);?></td>
                <td><?php echo $aRequestRow['creditmoneyrequest_reason'];?></td>
                <td>
                    <?php if($aRequestRow['creditmoneyrequest_modification_datetime']) :?>
                        <?php echo Phpfox::getTime('d/m/Y',$aRequestRow['creditmoneyrequest_modification_datetime']) ;?>
                    <?php endif;?>
                </td>
                <td><?php echo $aRequestRow['creditmoneyrequest_response'];?></td>
                <td>
                    <?php if($aRequestRow['creditmoneyrequest_status'] == 'pending') :?>
                        <a href="{permalink module='admincp.ecommerce.request-approved' id='id_'.$aRequestRow.creditmoneyrequest_id}">{phrase var='approve'}</a>
                        |
                        <a href="javascript:void(0);" onclick="javascript:denyRequest('<?php echo $aRequestRow['creditmoneyrequest_id'];?>');">{phrase var='deny'}</a>
                    <?php endif;?>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
{pager}
{else}
<div class="alert alert-info">
    {phrase var='no_requests_found'}
</div>
{/if}

{literal}
<script type="text/javascript">
    function denyRequest(id) {
        tb_show('', $.ajaxBox('ecommerce.getDenyRequestForm', 'height=800&width=500&id='+id));
    }
</script>
{/literal}