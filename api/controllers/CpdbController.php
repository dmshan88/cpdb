<?php
namespace app\controllers;

use Yii;
use app\models\cpdb\Routine;
use app\models\cpdb\ErrCode;


class CpdbController extends \yii\web\Controller
{

    public function actionTest()
    {
        $routineObj = new Routine;
        // $routineObj->readResults(1);
        var_dump($routineObj->updateGroupstat(1));
        var_dump($routineObj->getErrMsg());

        // return $routineObj->updateGroupstat(37);
        // if (!$routineObj->updateGroupstat(37)) {
        //     return $routineObj->getErrMsg();
        // }
        // var_dump($routineObj->updateGroupstat(37));
        // var_dump($routineObj->finishOrder(37));
        // var_dump($routineObj->getErrMsg());
        // var_dump($routineObj->finishgroup(216));
        // var_dump($routineObj->resetResult(37));


/*        $objPHPExcel = new \PHPExcel();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                                     ->setLastModifiedBy("Maarten Balliauw")
                                     ->setTitle("Office 2007 XLSX Test Document")
                                     ->setSubject("Office 2007 XLSX Test Document")
                                     ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                                     ->setKeywords("office 2007 openxml php")
                                     ->setCategory("Test result file");


        // Add some data
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Hello')
                    ->setCellValue('B2', 'world!')
                    ->setCellValue('C1', 'Hello')
                    ->setCellValue('D2', 'world!');

        // Miscellaneous glyphs, UTF-8
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A4', 'Miscellaneous glyphs')
                    ->setCellValue('A5', 'éàèùâêîôûëïüÿäöüç');

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle('Simple');


        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);


        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="01simple.xls"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;*/
    }

    public function actionShoworder()
    {
        $request = Yii::$app->request;

        if ($request->post('key') != 'showorder') {
            return ErrCode::retErr(ErrCode::KEY_WRONG);
        }

        $routineObj = new Routine;
        $orderarr = $routineObj->showOrders();
        $machinearr = $routineObj->showMachines();
        $panelarr = $routineObj->showPanels();
        return compact('orderarr', 'machinearr', 'panelarr');
    }

    public function actionShowreport()
    {
        $request = Yii::$app->request;
        $orderid = $request->post('orderid');
        $orderid = intval($orderid);

        if ($request->post('key') != 'showreport') {
            return ErrCode::retErr(ErrCode::KEY_WRONG);
        }

        $routineObj = new Routine;
        $reportarr = $routineObj->showReport($orderid);
        if ($reportarr === false ) {
            return ErrCode::retErr($routineObj->getErrCode());
        }
        return compact('reportarr');
    }
    public function actionNeworder()
    {
        $request = Yii::$app->request;
        $panelid = $request->post('panel');
        $panelid = intval($panelid);
        $panellot = $request->post('panellot');
        $panellot = trim($panellot);
        $machineid = $request->post('machine');
        $machineid = intval($machineid);
        $ordername = $request->post('order');
        $ordername = trim($ordername);

        $routineObj = new Routine;
        if ($routineObj->newOrder($panelid, $panellot, $machineid, $ordername)) {
            return [ 'ok' => true, 'msg' => 'order add success'];
        } else {
            return ErrCode::retErr($routineObj->getErrCode());
        }
    }

    public function actionNewgroup()
    {
        $request = Yii::$app->request;
        $caliid = $request->post('caliid');
        $caliid = intval($caliid);
        $calilot = $request->post('calilot');
        $calilot = trim($calilot);
        $orderid = $request->post('orderid');
        $orderid = intval($orderid);

        $routineObj = new Routine;
        if ($routineObj->newGroup($caliid, $calilot, $orderid)) {
            return [ 'ok' => true, 'msg' => 'group add lot success' ];
        } else {
            return ErrCode::retErr($routineObj->getErrCode());
        }
    }

    public function actionAddgroup()
    {
        $request = Yii::$app->request;
        $abandon = $request->post('abandon');
        $grouparr = $request->post('group');
        $orderid = $request->post('orderid');
        
        $routineObj = new Routine;
        if (count($abandon) && !$routineObj->kickResult($abandon)) {
            return ErrCode::retErr($routineObj->getErrCode());
        }

        if (count($grouparr)) {
            if ($routineObj->addGroup($grouparr,$orderid)) {
                return [ 'addgroup' => true, 'msg' => 'group add success' ];
            } else {
                return ErrCode::retErr($routineObj->getErrCode());
            }
        } else {
            if ($routineObj->finishOrder($orderid)) {
                return [
                    'finishorder' => true,
                    'msg' => 'finish order success'
                ];
            } else {
                return ErrCode::retErr($routineObj->getErrCode());
            }
        }
    }

    public function actionChangetarget(){
        $request = Yii::$app->request;
        $changetargetarr = $request->post('changetargetarr');

        $routineObj = new Routine;
        $ret = $routineObj->changeTarget($changetargetarr);
        if ($ret === false) {
            return ErrCode::retErr($routineObj->getErrCode());
        } else {
            return [ 
                'changetarget' => $ret,
                'msg' => "$ret target update"
            ];
        }
    }

    public function actionShowtarget()
    {
        $request = Yii::$app->request;

        if ($request->post('key') != 'showtarget') {
            return ErrCode::retErr(ErrCode::KEY_WRONG);
        }

        $routineObj = new Routine;
        $targetitemarr = $routineObj->showTargets();
        $calinamearr = $routineObj->showCaliNames();
        if ($targetitemarr === false || $calinamearr === false) {
            return ErrCode::retErr($routineObj->getErrCode());
        }
        return compact('targetitemarr', 'calinamearr');
    }

    public function actionUpdatekb(){
        $request = Yii::$app->request;
        $orderid = $request->post('orderid');
        $orderid = intval($orderid);
        $cpdbkb = $request->post('cpdbkb');
        
        $routineObj = new Routine;
        $ret = $routineObj->updateKB($cpdbkb, $orderid);
        if ($ret === false) {
            return ErrCode::retErr($routineObj->getErrCode());
        } else {
            return [ 
                'updatekb' => $ret,
                'msg' => "$ret record update"
            ];
        }
    }

    public function actionResetresult(){
        $request = Yii::$app->request;
        $orderid = $request->post('orderid');
        $orderid=intval($orderid);

        $routineObj = new Routine;
        $ret = $routineObj->resetResult($orderid);
        if ($ret === false) {
            return ErrCode::retErr($routineObj->getErrCode());
        } else {
            return [ 
                'resetok' => true,
                'msg' => "reset success"
            ];
        }
    }

    public function actionShowtest()
    {
        $request = Yii::$app->request;
        $orderid = $request->post('orderid');
        
        $routineObj = new Routine;
        $results = $routineObj->readResults($orderid);
        if ($results === false) {
            $readmsg = 'read error';
        }
        else {
            $readmsg = "read $results records";
        }
        
        $itemarr = $routineObj->showOrderItems($orderid);
        $grouplotarr = $routineObj->showOrderGroupLots($orderid);
        $caliarr = $routineObj->showOrderCaliLots($orderid);
        $cpdbkbarr = $routineObj->showOrderKBs($orderid);
        $dataarr = $routineObj->showOrderResults($orderid);
        if ($itemarr === false
            || $grouplotarr === false
            || $caliarr === false
            || $dataarr === false
        ) {
            return ErrCode::retErr($routineObj->getErrCode());
        }
        return compact(
            'readmsg',
            'itemarr',
            'grouplotarr',
            'caliarr',
            'cpdbkbarr',
            'dataarr'
        );
    }
}
