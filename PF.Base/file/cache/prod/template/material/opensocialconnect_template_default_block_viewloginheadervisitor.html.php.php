<?php defined('PHPFOX') or exit('NO DICE!'); ?>
<?php /* Cached: May 17, 2020, 12:25 am */ ?>

<?php echo '
<style>
div#opensocialconnect_holder_header
{
    margin-left: ';  echo $this->_aVars['iMarginLeft'];  echo 'px;
    margin-top: ';  echo $this->_aVars['iMarginTop'];  echo 'px;
    z-index: 9990;
    padding: 3px;
    display: inline-block;
} 

div#opensocialconnect_holder_header img
{
    width:';  echo $this->_aVars['iIconSize'];  echo 'px;
    height:';  echo $this->_aVars['iIconSize'];  echo 'px;
} 

div#opensocialconnect_holder_header .providers
{
   float:left;
   padding-left: 7px;
} 

</style>
<script>
function opensopopup(pageURL)
{
    tb_remove();
    var w = 990;
    var h = 560;
    var title ="socialconnectwindow";
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var newwindow = window.open (pageURL, title, \'toolbar=no,location=no,directories=no,status=no,menubar=no, scrollbars=yes,resizable=yes,copyhistory=no,width=\'+w+\',height=\'+h+\',top=\'+top+\',left=\'+left);
    if (window.focus) {newwindow.focus();}
    return newwindow;
};
</script>
'; ?>

<?php if (count ( $this->_aVars['aOpenProviders'] )): ?>
<div id="opensocialconnect_holder_header">
<?php if (count((array)$this->_aVars['aOpenProviders'])):  $this->_aPhpfoxVars['iteration']['opr'] = 0;  foreach ((array) $this->_aVars['aOpenProviders'] as $this->_aVars['index'] => $this->_aVars['aOpenProvider']):  $this->_aPhpfoxVars['iteration']['opr']++; ?>

<?php if ($this->_aPhpfoxVars['iteration']['opr'] <= $this->_aVars['iLimitView']): ?>
            <div class="providers" > <a href="javascript: void(opensopopup('<?php echo Phpfox::getLib('phpfox.url')->makeUrl('opensocialconnect', array('service' => $this->_aVars['aOpenProvider']['name'])); ?>'));" title="<?php echo $this->_aVars['aOpenProvider']['title']; ?>"><img src="<?php echo $this->_aVars['sCoreUrl']; ?>module/opensocialconnect/static/image/<?php echo $this->_aVars['aOpenProvider']['name']; ?>.png" alt="<?php echo $this->_aVars['aOpenProvider']['title']; ?>" /></a> </div>
<?php if ($this->_aPhpfoxVars['iteration']['opr'] == $this->_aVars['iLimitView']): ?>
            <div class="providers"><a class="inlinePopup usingapi" title="<?php echo _p('module_opensocialconnect'); ?>" href="#?call=opensocialconnect.viewMore&amp;width=410"><img style="padding-top: 0px;" src="<?php echo $this->_aVars['sCoreUrl']; ?>module/opensocialconnect/static/image/more.png" alt="" /></a></div>
<?php endif; ?>
<?php endif; ?>
<?php endforeach; endif; ?>
</div>
<?php endif; ?>

