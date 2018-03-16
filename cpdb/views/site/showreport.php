<?php

use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Report';
// $this->params['breadcrumbs'][] = $this->title;
?>

    <table border="1">
        <tr> 
            <td> name: <?= $reportarr['panel'] ?> </td>
            <td> lot:  <?= $reportarr['panellot'] ?> </td>
        </tr>
        <tr>
            <td></td>
            <td> order:  <?= $reportarr['ordername'] ?> </td>
        </tr>
        <tr></tr>
        <tr>
            <td> date:  <?= $reportarr['startdate'] ?> </td>
            <td> machine:  <?= $reportarr['machine'] ?> # </td>
        </tr>
        <tr>
            <td> date:  <?= $reportarr['finishdate'] ?> </td>
            <td>  </td>
        </tr>
        </table>
        <table border="1">
            
        <?php 
            for ($i=0; $i < $rowcnt ; $i++) {
                echo "<tr>";
                for ($j=0; $j < $colcnt; $j++) { 
                    echo "<td>".$reporttable[$i][$j]."</td>";
                }
                echo "</tr>";
            }
        ?>
            
        
    </table>

