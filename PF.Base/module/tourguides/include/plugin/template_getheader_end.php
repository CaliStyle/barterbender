<?php
$aYnTourPosition = storage()->get('yntour_position');
$sData .= '<script> 
yntourPositionRight = "' . (isset($aYnTourPosition->value->right) ? $aYnTourPosition->value->right : '0.04') . '";
yntourPositionTop = "' . (isset($aYnTourPosition->value->top) ? $aYnTourPosition->value->top : '0.06') . '";
</script>';
