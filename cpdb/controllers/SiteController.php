<?php

namespace app\controllers;
use Yii;
use yii\data\ArrayDataProvider;
// use yii\data\ActiveDataProvider;
// use app\models\Cpdborder;
// use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use app\models\Report;
// use yii\db\Query;
class SiteController extends \yii\web\Controller
{
    public function actionTest()
    {


    }
    public function actionIndex()
    {
        $urlpara = Yii::$app->params['apiurl'];
        $request = Yii::$app->request;
        $msg = '';
        //add new order
        if($request->isPost){
            $url = $urlpara['neworder'];
            $post_data = [
                'panel' => $request->post('panel'),
                'panellot' => $request->post('panellot'),
                'machine' => $request->post('machine'),
                'order' => $request->post('ordername'),
            ];
            $ret = getServerError(
                httppost($url, $post_data),
                [ 'ok', 'msg' ]
            );
            $msg = (is_array($ret)) ? $ret['msg'] : $msg;
        }
        $url = $urlpara['showorder'];
        $post_data = [
            'key'=>'showorder',
        ];
        $ret = getServerError(
            httppost($url, $post_data),
            [ 'orderarr', 'machinearr', 'panelarr' ]
        );
        if (is_array($ret)) {
            $dataProvider = new ArrayDataProvider([
                'allModels' => $ret['orderarr'],
                'pagination' => [
                    'pageSize' => 40,
                ],
            ]);
            return $this->render('index', [
                'msg' => $msg,
                'dataProvider' => $dataProvider,
                'machinearr' => $ret['machinearr'],
                'panelarr' => $ret['panelarr'],
            ]);
        } else {
            throw new ServerErrorHttpException($ret, 500);
        }
    }

    public function actionChangetarget()
    {
        $urlpara = Yii::$app->params['apiurl'];
        $request = Yii::$app->request;
        $msg = '';
        if($request->isPost){
            $changetargetarr = $request->post('changetarget');
            foreach ($changetargetarr as $itemid => $value) {
                foreach ($value as $caliid => $target) {
                    if(!is_numeric($target)){
                        unset($changetargetarr[$itemid][$caliid]);
                    }
                }
                if(empty($changetargetarr[$itemid])){
                    unset($changetargetarr[$itemid]);
                }
            }
            if(count($changetargetarr)){
                $url = $urlpara['changetarget'];
                $post_data = [
                    'changetargetarr'=>$changetargetarr,
                ];
                $ret = getServerError(
                    httppost($url, $post_data, 1),
                    [ 'changetarget', 'msg' ]
                );
                $msg = (is_array($ret)) ? $ret['msg'] : $msg;
            }
        }
        $url = $urlpara['showtarget'];
        $post_data = [
            'key'=>'showtarget',
        ];
        $ret = getServerError(
            httppost($url, $post_data),
            [ 'targetitemarr', 'calinamearr']
        );
        if (is_array($ret)) {
            $dataProvider = new ArrayDataProvider([
                'allModels' => $ret['targetitemarr'],
                'pagination' => [
                    'pageSize' => 40,
                ],
            ]);
            return $this->render('changetarget', [
                'msg' => $msg,
                'dataProvider' => $dataProvider,
                'calinamearr' => $ret['calinamearr'],
            ]);
        } else {
            throw new ServerErrorHttpException($ret, 500);
        }
    }

     public function actionShowreport()
    {
        $urlpara = Yii::$app->params['apiurl'];
        $request = Yii::$app->request;
        $orderid = $request->get('id');
        $msg = $request->get('msg');

        if(empty($orderid)){
            throw new NotFoundHttpException('empty orderid',404);
        }
         $url = $urlpara['showreport'];
        $post_data = [
            'orderid' => $orderid,
            'key' => 'showreport',
        ];
        $ret = getServerError(
            httppost($url, $post_data, 1),
            [ 'reportarr']
        );
        $reportObj = new Report;
        if ($reportObj->Inidata($ret)) {
            $this->layout = false;
            return $this->render('showreport',[
                'orderid' => $orderid,
                'ret' => $ret,
                'msg' => $msg,
                'reportarr' => $ret['reportarr'],
                'reporttable' => $reportObj->getTabelData(),
                'rowcnt' => $reportObj->rowcnt,
                'colcnt' => $reportObj->colcnt,
            ]);
        }
    }

    public function actionShowtest()
    {
        $urlpara = Yii::$app->params['apiurl'];
        $request = Yii::$app->request;
        $orderid = $request->get('id');
        $msg = $request->get('msg');

        if(empty($orderid)){
            throw new NotFoundHttpException('empty orderid',404);
        }
        if($request->isPost){
            //group & kick
            $grouparr=$request->post('group');
            $abandonarr=$request->post('abandon');
            $caliid=$request->post('caliid');
            $calilot=$request->post('calilot');
            $cpdbkb=$request->post('cpdbkb');

            if ($orderid!=$request->post('orderid')) {
                $msg='bad orderid';
            } elseif(!empty($calilot)) {
                $url = $urlpara['newgroup'];
                $post_data = compact('caliid', 'calilot', 'orderid');
                $ret = getServerError(
                    httppost($url, $post_data),
                    [ 'ok', 'msg' ]
                );
                $msg = (is_array($ret)) ? $ret['msg'] : $msg;
            } elseif(!empty($cpdbkb)) {
                // var_dump($cpdbkb);
                foreach ($cpdbkb as $key => $value) {
                    if(empty($value['lot'])||empty($value['k'])){
                        unset($cpdbkb[$key]);
                    }
                }
                $url = $urlpara['updatekb'];
                $post_data = [
                    'cpdbkb' => $cpdbkb,
                    'orderid' => $orderid,
                ];
                $ret = getServerError(
                    httppost($url, $post_data, 1),
                    [ 'updatekb', 'msg' ]
                );
                $msg = (is_array($ret)) ? $ret['msg'] : $msg;
            } elseif(!empty($grouparr)||!empty($abandonarr)) {
                // echo "addgroup";
                if(count($grouparr)) {
                    foreach ($grouparr as $key => $value) {
                        if(empty($value)){
                            unset($grouparr[$key]);
                        }
                    }
                }
                exit;
                $url=$urlpara['addgroup'];
                $post_data = [
                    'group'=>$grouparr,
                    'abandon'=>$abandonarr,
                    'orderid'=>$orderid,
                ];
                $curlret = httppost($url, $post_data, 1);
                $msg = is_array($ret = getServerError($curlret, [ 'finishorder', 'msg' ]))
                    ? $ret['msg'] 
                    : (
                        is_array($ret = getServerError($curlret, [ 'addgroup', 'msg' ]))
                        ? $ret['msg']
                        : $msg
                    );
            }
        }
        $url = $urlpara['showtest'];
        $post_data = [
            'orderid' => $orderid,
        ];
        $ret = getServerError(
            httppost($url, $post_data, 1),
            [ 'readmsg', 'itemarr', 'grouplotarr', 'caliarr', 'cpdbkbarr', 'dataarr']
        );
        if (is_array($ret)) {
            $dataProvider = new ArrayDataProvider([
                'allModels' => $ret['dataarr'],
                'pagination' => [
                    'pageSize' => 20,
                ],
                'sort' => [
                    'attributes' => ['id', 'name'],
                ],
            ]);
            $dataProvider1 = new ArrayDataProvider([
                'allModels' => $ret['cpdbkbarr'],
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
            return $this->render('showtest', [
                    'dataProvider' => $dataProvider,
                    'dataProvider1' => $dataProvider1,
                    'orderid' => $orderid,
                    'msg' => $msg,
                    'readmsg' => $ret['readmsg'],
                    'itemarr' => $ret['itemarr'],
                    'grouplotarr' => $ret['grouplotarr'],
                    'caliarr' => $ret['caliarr'],
            ]);
        } else {
            throw new ServerErrorHttpException($ret, 500);
        }
    }
    public function actionResetresult()
    {
        $urlpara = Yii::$app->params['apiurl'];
        $request = Yii::$app->request;
        $orderid = $request->get('orderid');
        $url = $urlpara['resetresult'];
        $post_data = compact('orderid');
        $ret = getServerError(
            httppost($url, $post_data),
            [ 'resetok', 'msg' ]
        );
        $msg = (is_array($ret)) ? $ret['msg'] : $msg;
        return $this->redirect(['site/showtest', 'id'=>$orderid, 'msg'=>$msg]);
    }
}
function httppost($url, $data = NULL, $json = false)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        if($json && is_array($data)){
            $data = json_encode( $data );
    }
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    if($json){ //发送JSON数据
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER,
        array(
            'Content-Type: application/json; charset=utf-8',
            'Content-Length:' . strlen($data))
            );
        }
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($curl);

    $errorno = curl_errno($curl);
    $httpCode = curl_getinfo($curl,CURLINFO_HTTP_CODE);
    curl_close($curl);
    
    if ($errorno) {
        return array('httperrno' => $errorno);
    } elseif ($httpCode != 200) {
        return array('httpstatus' => $httpCode);
    } else {
        return json_decode($res, true);
    }
}
function getServerError($ret= [] ,$keyarr = [])
{
    if (is_array($ret) && is_array($keyarr) && $ret && $keyarr) {
        if (isset($ret['httperrno']) || isset($ret['httpstatus'])) {
            return 'curl or server error';
        } elseif (isset($ret['errcode']) && isset($ret['errmsg']) && is_string($ret['errmsg'])) {
            return $ret['errmsg'];
        } else {
            foreach ($keyarr as $key) {
                if (!array_key_exists($key, $ret)) {
                    return 'para key not exist';
                }
            }
            return $ret;
        }
    } else {
        return 'para is not array';
    }
}