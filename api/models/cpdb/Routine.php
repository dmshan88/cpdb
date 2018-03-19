<?php
namespace app\models\cpdb;

use Yii;
// use yii\db\Query;
use app\models\cpdb\ErrCode;
use app\models\gii\Cpdborder;
use app\models\gii\Machine;
use app\models\gii\Panel;
use app\models\gii\Panelitem;
use app\models\gii\Paneltest;
use app\models\gii\Panelresult;
use app\models\gii\Cpdbkb;
use app\models\gii\Cpdbgroup;
use app\models\gii\Cpdbcalc;
use app\models\gii\Calibrator;
use app\models\gii\Item;
use app\models\gii\Itemcali;

class Routine
{
    private $errCode = ErrCode::OK;
    private $errMsg = '';

    public function test($value='')
    {
    }

    public function getErrCode()
    {
        return $this->errCode;
    }

    public function getErrMsg()
    {
        return $this->errMsg;
    }

    public function showReport($orderid = '')
    {
        $order = Cpdborder::findOne($orderid);
        if (empty($orderid) || !$order) {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'order no exist';
            return false;
        }
        $kbarr = [];
        $itemcnt = 0;
        foreach ($order->cpdbkbs as $cpdbkb) {
            $itemcnt++;
            $itemid = $cpdbkb->item_id;
            $kbarr[$itemid] = [
                'itemid' => $cpdbkb->item_id,
                'item' => $cpdbkb->item->name,
                'unit' => $cpdbkb->item->unit,
                'lot' => $cpdbkb->lot,
                'kvalue0' => $cpdbkb->kvalue0,
                'bvalue0' => $cpdbkb->bvalue0,
                'kvalue1' => $cpdbkb->kvalue1,
                'bvalue1' => $cpdbkb->bvalue1,
                'rvalue1' => $cpdbkb->rvalue1,
                'kstr' => $cpdbkb->kstr,
                'bstr' => $cpdbkb->bstr,
                'avercal2' => $cpdbkb->avercal2,
            ];
        }
        foreach ($order->panel->panelitems as $panelitem) {
            $itemid = $panelitem->item_id;
            $kbarr[$itemid]['position'] = $panelitem->position;
            $kbarr[$itemid]['hole'] = $panelitem->hole;
        }
        $grouparr = [];
        // $resultarr = [];
        // $totaltestcnt = 0;
        $testcntarr0 = [];
        $testcntarr1 = [];
        $testcntarr2 = [];
        foreach ($order->cpdbgroups as $cpdbgroup) {
            $groupid = $cpdbgroup->id;
            $averarr = [];
            foreach ($cpdbgroup->cpdbcalcs as $cpdbcalc) {
                $itemid = $cpdbcalc->item_id;
                if (!$cpdbcalc->xcount) {
                    continue;
                }
                $caliarr = $cpdbcalc->item
                    ->getCalibrators()
                    ->select('id')
                    ->column();
                if (!in_array($cpdbgroup->calibrator_id, $caliarr)) {
                    continue;
                }
                $averarr[$itemid] = [
                    'target' => $cpdbcalc->target,
                    'xvalue' => $cpdbcalc->xvalue,
                    // 'xsum' => $cpdbcalc->xsum,
                    // 'xcount' => $cpdbcalc->xcount,
                    // 'xaver' => $cpdbcalc->xsum/$cpdbcalc->xcount,
                ];
            }

            $resultarr = [];
            $testcnt = 0;
            foreach ($cpdbgroup->paneltests as $paneltest) {
                $testid = $paneltest->id;
                $testcnt++;
                foreach ($paneltest->panelresults as $panelresult) {
                    $itemid = $panelresult->item_id;
                    if (!isset($averarr[$itemid])) {
                        continue;
                    }
                    $resultarr[$testid][$itemid] = [
                        'result' => $panelresult->result,
                        'abandon' => $panelresult->abandon,
                        // 'abnormal' => $panelresult->abnormal,
                    ];
                }
            }
            if (!$averarr || !$resultarr) {
                continue;
            }
            $grouparr[$groupid] = [
                'calibratorlot' => $cpdbgroup->calibratorlot,
                'calibrator' => $cpdbgroup->calibrator->name,
                'iscal2' => empty($cpdbgroup->calibrator_id),
                'stat' => $cpdbgroup->stat,
                'recheck' => $cpdbgroup->recheck,
                // 'calibrator' => $cpdbgroup->recheck,
                'testcnt' => $testcnt,
                'averarr' => $averarr,
                'resultarr' => $resultarr,
            ];
            if ($cpdbgroup->calibrator_id) {
                $testcntarr0[$groupid] = $testcnt;
            } elseif ($cpdbgroup->recheck == 'N') {
                $testcntarr1[$groupid] = $testcnt;
            } else {
                $testcntarr2[$groupid] = $testcnt;
            }
        }
        // exit;
        // return true;
        return [
            'ordername' => $order->ordername,
            'panellot' => $order->panellot,
            'startdate' => $order->startdate,
            'finishdate' => $order->finishdate,
            'orderstat' => $order->stat,
            'machine' => $order->machine->name,
            'panel' => $order->panel->showname,
            'paneltype' => $order->panel->type,
            'itemcnt' => $itemcnt,
            'kbarr' => $kbarr,
            'grouparr' => $grouparr,
            'testcntarr0' => $testcntarr0,
            'testcntarr1' => $testcntarr1,
            'testcntarr2' => $testcntarr2,
            // 'resultarr' => $resultarr,
        ];
    }

    public function showOrders()
    {
        return Cpdborder::find()
            ->select([
                'cpdborder.id',
                'cpdborder.ordername',
                'cpdborder.panellot',
                'cpdborder.startdate',
                'cpdborder.finishdate',
                'cpdborder.stat',
                'panelname' => 'panel.showname',
                'machinename' => 'machine.name',
            ])
            ->innerJoinWith('panel',false)
            ->innerJoinWith('machine',false)
            ->orderBy('startdate desc')
            ->indexBy('id')
            ->asArray()
            ->all();
    }

    public function showMachines()
    {
        return Machine::find()
            ->select(['name','id'])
            ->where(['stat' => 'ON'])
            ->indexBy('id')
            ->orderBy('id')
            ->column();
    }
    public function showPanels()
    {
        return Panel::find()
            ->select(['showname','id'])
            ->indexBy('id')
            ->orderBy('id')
            ->column();
    }

    public function showOrderItems($orderid = '')
    {
        $itemarr = Cpdborder::findOne($orderid)
            ->getPanel()
            ->select([ 'item.name', 'item.id'])
            ->innerJoinWith('items',false)
            ->orderBy('panelitem.position')
            ->indexBy('id')
            ->column();
        if (empty($itemarr)) {
            $this->errCode = ErrCode::INNER_ERR;
            $this->errMsg = 'item empty';
            return false;
        } else {
            return $itemarr;
        }

    }
    public function showOrderGroupLots($orderid = '')
    {

            $retarray = [];
            $order = Cpdborder::findOne($orderid);
            if (empty($order)) {
                return [];
            }
            foreach ($order->cpdbgroups as $group) {
                if ($group->calibratorlot) {
                    $del = ($group->stat == 'DEL') ? ' (BAD)' : '' ;
                    $retarray[$group->id] = sprintf("%s - %s%s", $group->calibrator->name, $group->calibratorlot, $del);
                }
            }
            return $retarray;
    }

    public function showOrderCaliLots($orderid = '')
    {
        // return [];
        return Cpdbgroup::find()
            ->select([ 'calibrator.name', 'calibrator.id' ])
            ->innerJoinWith('calibrator', false)
            ->where([
                'cpdbgroup.cpdborder_id' => $orderid,
                'cpdbgroup.stat' => 'ON',
                'cpdbgroup.calibratorlot' => null,
            ])
            ->orderBy('id')
            ->indexBy('id')
            // ->distinct()
            ->column();
    }

    public function showOrderKBs($orderid = '')
    {
        return Cpdborder::findOne($orderid)
            ->getCpdbkbs()
            ->select([
                'cpdbkb.item_id',
                'item.name',
                'cpdbkb.lot',
                'cpdbkb.kvalue0',
                'cpdbkb.bvalue0',
                'cpdbkb.kvalue1',
                'cpdbkb.bvalue1',
                'cpdbkb.rvalue1',
                'cpdbkb.kstr',
                'cpdbkb.bstr',
            ])
            ->innerJoinWith('item', false)
            ->asArray()
            ->all();
    }

    public function showOrderResults($orderid = '')
    {
        $orderrecord = Cpdborder::findOne($orderid);
        $groupitem = $orderrecord
            ->getCpdbgroups()
            ->select(['cpdbgroup.id'])
            ->column();
        $testarr = Paneltest::find()
            ->with([ 'panelresults' => function ($query)
                {
                    $query->select([
                        'paneltest_id' => 'paneltest_id',
                        'itemid' => 'item_id',
                        'result' => 'result',
                        'abandon' => 'abandon',
                    ])->indexBy('itemid');
                }, 
                'cpdbgroup'
            ])
            ->where([
                'paneltest.groupid' => $groupitem,
            ])
            ->orWhere([
                'paneltest.groupid' => null,
                'paneltest.machine_id' => $orderrecord['machine_id'],
                'paneltest.panel_id' => $orderrecord['panel_id'],
                'paneltest.type' => 'CPDB',
            ])
            ->asArray()
            ->all();
        $data = [];
        foreach ($testarr as $value) {
            $id = $value['id'];
            $data[$id]['chkdatetime'] = $value['chkdatetime'];
            $data[$id]['groupid'] = isset($value['cpdbgroup']['id']) ? $value['cpdbgroup']['id'] : null ;
            $data[$id]['groupstat'] = isset($value['cpdbgroup']['stat']) ? $value['cpdbgroup']['stat'] : null ;
            $data[$id]['calibrator'] = isset($value['cpdbgroup']['calibrator_id']) ? $value['cpdbgroup']['calibrator_id'] : null ;
            $data[$id]['calibratorlot'] = isset($value['cpdbgroup']['calibratorlot']) ? $value['cpdbgroup']['calibratorlot'] : null ;
            $data[$id]['results'] = isset($value['panelresults']) ? $value['panelresults']: null ;
        }
        return $data;
    }

    public function newOrder($panelid = '', $panellot = '', $machineid = '', $ordername = '')
    {
        $order = new Cpdborder();
        $transaction = $order->getDb()->beginTransaction();
        try {
            $order->setAttributes([
                'ordername' => $ordername,
                'machine_id' => $machineid,
                'panel_id' => $panelid,
                'panellot' => $panellot,
                'startdate' => date('Y-m-d'),
                'stat' => 'ON',
            ]);
            if (!$order->save()) {
                throw new \Exception('order add fail', 2);
            }
            $orderid = $order->id;
            //cpdbkb
            $items = Panel::findOne($panelid)
                ->getItems()
                ->select('item.id')
                ->column();
            foreach ($items as $item) {
                $cpdbkb = new Cpdbkb();
                $cpdbkb->setAttributes(['item_id' => $item,'cpdborder_id' => $orderid]);
                if (!$cpdbkb->save()) {
                    throw new \Exception('cpdbkb add fail', 1);
                }
            }
            //cpdbgroup
            $ret = Cpdbgroup::find()
                ->where([ 'cpdborder_id' => $orderid ])
                ->exists(); 
            if ($ret) {
                throw new \Exception('group order exist', 1);
            }
            $calis = Panel::findOne($panelid)
                ->getItems()
                ->select('calibrator.id')
                ->innerJoinWith('calibrators',false)
                ->asArray()
                ->distinct()
                ->orderBy(['id' => SORT_DESC])
                ->column();
            $calis['c2'] = 0;
            $calis['c3'] = 0;
            $calis['c4'] = 0;
            $itemcalis = Panel::findOne($panelid)
                ->getItems()
                ->select([ 'itemcali.item_id','itemcali.calibrator_id','itemcali.target', ])
                ->innerJoinWith('itemcalis',false)
                ->asArray()
                ->all();

            $cpdbcalcarr = array();
            foreach ($calis as $key => $caliid) {
                $stat = ($key === 'c3' || $key === 'c4') ? 'READY' : 'ON';
                $cpdbgroup = new Cpdbgroup();
                $cpdbgroup->setAttributes([
                    'cpdborder_id' => $orderid,
                    'calibrator_id' => $caliid,
                    'recheck' => ($key === 'c4') ? 'Y' : 'N' ,
                    'stat' => $stat,
                    'inistat' => $stat,
                ]);
                if (!$cpdbgroup->save()) {
                    throw new \Exception('cpdbgroup add fail', 1);
                }
                foreach ($itemcalis as $key1 => $value) {
                    if ($value['calibrator_id'] == $caliid) {
                        $cpdbcalcarr[] = [
                            'cpdbgroup_id' => $cpdbgroup->id,
                            'item_id' => $value['item_id'],
                            'target' => $value['target'],
                        ];
                        if ($caliid) {
                            unset($itemcalis[$key1]);
                        }
                    }
                }
            }
            //cpdbcalc
            if (empty($cpdbcalcarr)) {
                throw new \Exception('cpdbcalcarr empty', 1);
            }
            foreach ($cpdbcalcarr as $value) {
                $cpdbcalc = new Cpdbcalc();
                $cpdbcalc->setAttributes($value);
                if (!$cpdbcalc->save()) {
                    throw new \Exception('Cpdbcalc add fail', 1);
                }
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg =$e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }

    public function newGroup($caliid = '', $calilot = '', $orderid = '')
    {
        if (empty($calilot) || empty($orderid)) {
            $this->errCode = ErrCode::INPUT_ERROR;
            $this->errMsg = 'input error';
            return false;
        }

        $groupobj = Cpdbgroup::find()
            ->where([
                'cpdborder_id' => $orderid,
                'calibrator_id' => $caliid,
                'calibratorlot' => null,
                'stat' => 'ON',
            ])
            ->orderBy('id')
            ->one();
        if ($groupobj && $calilot) {
            $groupobj->calibratorlot = $calilot;
            return $groupobj->save();
        } else {
            return false;
        }

    }
    public function kickResult($abandon = [])
    {
        if(!is_array($abandon) || !count($abandon)){
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'abandon array error';
            return false;
        }
        $abandoncnt = 0;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($abandon as $key => $value) {
                foreach ($value as $key1 => $value1) {
                    $panelresult = Panelresult::find()
                        ->where([ 'paneltest_id' => $key, 'item_id' => $key1,])
                        ->one();
                    ;
                    if (!$panelresult) {
                        throw new \Exception("kick empty", 1);
                    }
                    $panelresult->abandon = 'Y';
                    if($panelresult->save())
                        $abandoncnt++;
                    else
                        throw new \Exception("kick error", 1);
                }
            }
            $transaction->commit();
            return $abandoncnt;
        } catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }

    public function addGroup($grouparr = [], $orderid = '')
    {
        if(!is_array($grouparr) || !count($grouparr)){
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'group array error';
            return false;
        }
        foreach ($grouparr as $id => $groupid) {
            $grouprecord = Cpdbgroup::find()
                ->where([
                    'cpdbgroup.id'=>$groupid,
                    'cpdbgroup.stat'=>'ON',
                    'cpdbgroup.cpdborder_id'=>$orderid,
                ])
                ->exists();
            if(!$grouprecord){
                $this->errCode = ErrCode::INNER_ERR;
                $this->errMsg = 'no active group';
                return false;
            }
        }
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($grouparr as $id => $groupid) {
                $paneltest = Paneltest::findOne($id);
                if(!$paneltest)
                    throw new \Exception("paneltest empty", 1);
                $paneltest->groupid = $groupid;
                if(!$paneltest->save())
                    throw new \Exception("group add error", 1);
            }
            $transaction->commit();
            return count($grouparr);
        } catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }

    public function finishOrder($orderid = '')
    {
        $order = Cpdborder::findOne($orderid);
        if (!$order) {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'order not exist';
            return false;
        }
        $activegrouparr = $order->getCpdbgroups()
            ->where([ 'stat' => 'ON' ])
            ->asArray()
            ->all();
        if (empty($activegrouparr)) {
            if ($this->cpdbcalckb($orderid)) {
                return true;
            } else {
                $this->errCode = ErrCode::FUNC_RET_FALSE;
                $this->errMsg = 'calc kb false';
                return false;
            }
        }
        foreach ($activegrouparr as $key => $value) {
            $ret = $this->finishgroup($value['id']);
/*            if ($value['recheck'] == 'Y' && $ret) {
                # code...
            }*/

        }
        $this->updateGroupstat($orderid);
        $this->errCode = ErrCode::FUNC_RET_FALSE;
        $this->errMsg = 'order not finish';
        return false;
    }

    public function updateGroupstat($orderid = '')
    {
        $order = Cpdborder::findOne($orderid);
        if (!$order) {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'order not exist';
            return false;
        }
        $flag = $order->getCpdbgroups()
            ->where([
                'stat' => 'ON',
            ])
            ->exists();
        if ($flag) {
            $this->errCode = ErrCode::INNER_ERR;
            $this->errMsg = 'active group exists';
            return false;
        }
        $groups = $order->getCpdbgroups()
            ->where([
                'stat' => 'OFF',
                'recheck' => 'N',
                'calibrator_id' => 0,
            ])
            ->all();
        if (!$groups || count($groups) < 2) {
            $this->errCode = ErrCode::INNER_ERR;
            $this->errMsg = 'group not enough';
            return false;
        }
        $groupcnt = count($groups);
        $barr = [];
        $threshold = [];
        foreach ($groups as $key => $group) {
            foreach ($group->cpdbcalcs as $cpdbcalc) {
                $itemid = $cpdbcalc->item_id;
                $barr[$itemid][$key] = $cpdbcalc->bvalue;
                $threshold[$itemid] = $cpdbcalc->item->threshold;
            }
        }
        // var_dump($barr);
        foreach ($barr as $itemid => $value) {
            $ret = chksubval($value, $threshold[$itemid], $groupcnt);
            // var_dump($ret);
            if ($ret === false) {
                $this->errCode = ErrCode::FUNC_RET_FALSE;
                $this->errMsg = 'fun chksubval()  ret false';
                return false;
            } elseif ($ret === 0) {

            } elseif (in_array($ret, [1, 2, 3])) {
                if ($groupcnt == 2) {
                    // $groups[]
                    $group3 = $order->getCpdbgroups()
                        ->where([
                            'stat' => 'READY',
                            'recheck' => 'N',
                            'calibrator_id' => 0,
                        ])
                        ->one();
                    if (!$group3) {
                        $this->errCode = ErrCode::INNER_ERR;
                        $this->errMsg = 'no find group3';
                        return false;
                    }
                    $group3->stat = 'ON';
                    return $group3->save();
                } elseif ($groupcnt == 3) {
                    if (!isset($groups[$ret-1])) {
                        $this->errCode = ErrCode::INNER_ERR;
                        $this->errMsg = 'group array key not exist';
                        return false;
                    } else {
                        $badgroup = $groups[$ret-1];
                        $badgroup->stat = 'DEL';
                        $badgroup->save();
                        break;
                    }
                }
            } elseif ($ret == 4) {
                //

            } else {
                $this->errCode = ErrCode::FUNC_RET_FALSE;
                $this->errMsg = 'fun chksubval()  ret bad value';
                return false;
            }
        }
        //recheck
        $grouprecheck = $order->getCpdbgroups()
            ->where([
                'stat' => 'READY',
                'recheck' => 'Y',
            ])
            ->one();
        if (!$grouprecheck) {
            $this->errCode = ErrCode::INNER_ERR;
            $this->errMsg = 'no find group recheck';
            return false;
        }
        $grouprecheck->stat = 'ON';
        return $grouprecheck->save();
    }

    public function finishgroup($groupid = '')
    {
        $grouprecord = Cpdbgroup::findOne($groupid);
        if(empty($grouprecord)){
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'empty group';
            return false;
        }
        $resultarr = Panelresult::find()
            ->select(['count' => 'count(*)', 'sum' => 'sum(panelresult.result)','panelresult.item_id'])
            ->innerJoinWith('paneltest', false)
            // ->innerJoinWith('itemcali', false)
            ->where([
                'paneltest.groupid' => $groupid,
                'panelresult.abandon' => 'N',
                // 'itemcali.calibrator_id' => $grouprecord['calibrator_id'],
            ])
            ->groupBy('panelresult.item_id')
            ->indexBy('item_id')
            ->asArray()
            ->all();
        if(empty($resultarr)){
            $this->errCode = ErrCode::INNER_ERR;
            $this->errMsg = 'empty tests';
            return false;
        }
        if(empty($grouprecord['cpdborder_id'])){
            //cal2
            $cal2order = Cpdbgroup::find()
                ->where([
                    'cpdborder_id'=>$grouprecord['cpdborder_id'],
                    'calibrator_id'=>0,
                ])
                ->andWhere(['<=','id',$groupid])
                ->count();
        }
        else{
            //other
            $cal2order = 0;
        }
        // var_dump($resultarr);
        $needcnt = $cal2order ? 2 : 2;
        foreach ($resultarr as $key => $value) {
            if(array_key_exists('count', $value)
                && is_numeric($value['count'])
                && $value['count'] > 1
                && $value['count'] >= $needcnt) {

            }
            else{
                $this->errCode = ErrCode::INNER_ERR;
                $this->errMsg = 'error or item count less '.$needcnt;
                return false;
            }
        }
        // return $resultarr;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($grouprecord->getCpdbcalcs()->all() as $cpdbcalc) {
                $itemid = $cpdbcalc->item_id;
                $cpdbcalc->xcount = $resultarr[$itemid]['count'];
                $cpdbcalc->xsum = $resultarr[$itemid]['sum'];
                $cpdbcalc->xvalue = $resultarr[$itemid]['sum']/$resultarr[$itemid]['count'];
                if (empty($grouprecord->calibrator_id) && $cpdbcalc->target) {
                    $cpdbcalc->bvalue = abs($cpdbcalc->xvalue - $cpdbcalc->target) / $cpdbcalc->target *100;
                }
                $cpdbcalc->save();
            }
            $grouprecord->stat = 'OFF';
            // var_dump($grouprecord);
            if(!$grouprecord->save()){
                $msg='group stat change error';
                throw new \Exception($msg,1);
            }
            $transaction->commit();
            return true;
        }catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }

    public function cpdbcalckb($orderid='')
    {
        $orderid = intval($orderid);
        $connection = Yii::$app->db;
        $orderrecord = Cpdborder::findOne($orderid);
        if (!isset($orderrecord['stat']) || $orderrecord['stat'] != 'ON') {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'order not exist';
            return false;
        }

        $cpdbcalcarr = Cpdbcalc::find()
            ->select([
                'cpdbgroup.calibrator_id',
                'cpdbcalc.item_id',
                'cpdbcalc.target',
                'sum'=>'sum(cpdbcalc.xsum)',
                'count'=>'sum(cpdbcalc.xcount)',
                'item.calithree',
            ])
            ->innerJoinWith('cpdbgroup', false)
            ->innerJoinWith('item', false)
            ->where([
                'cpdbgroup.cpdborder_id' => $orderid,
                'cpdbgroup.stat' => 'OFF',
                'cpdbgroup.recheck' => 'N',
            ])
            ->groupBy(['calibrator_id', 'item_id'])
            // ->indexBy('item_id')
            ->asArray()
            ->all();
        // var_dump($cpdbcalcarr);
        // exit;
        $avercal2arr = [];
        $calckbarr = [];
        foreach ($cpdbcalcarr as $key => $value) {
            $itemid = $value['item_id'];
            $calckbarr[$itemid]['itemid'] = $itemid;
            $calckbarr[$itemid]['calicount'] = $value['calithree'];
            $tmpaver = $value['sum']/$value['count'];
            $calckbarr[$itemid]['x'][] = $tmpaver; 
            $calckbarr[$itemid]['y'][] = $value['target'];
            if (empty($value['calibrator_id'])) {
                $avercal2arr[$itemid] = $tmpaver;
            }
        }
/*        var_dump($calckbarr);
        exit;*/
        foreach ($calckbarr as $key => $value) {
            if ($value['calicount'] != count($value['x'])) {
                $this->errCode = ErrCode::INNER_ERR;
                $this->errMsg = 'item:'.$key.' cali count not enough ,need '.$value['calicount'].',now: '.count($value['x']);
                return false;
            }
            elseif (count(array_unique($value['x'])) < count($value['x'])) {
                unset($calckbarr[$key]);
            }
            else{
                $ret = calckbr2($value['x'],$value['y']);
                if(is_array($ret) && array_key_exists('calc', $ret) ){
                    if($ret['calc']){
                        $calckbarr[$key]['kvalue1']=$ret['k'];
                        $calckbarr[$key]['bvalue1']=$ret['b'];
                        $calckbarr[$key]['rvalue1']=$ret['r2'];
                    }
                    else{
                        $this->errCode = ErrCode::INNER_ERR;
                        $this->errMsg ="item: $key ".$ret['msg'];
                        return false;
                    }
                }
                else{
                    $this->errCode = ErrCode::INNER_ERR;
                    $this->errMsg ='compute kb error';
                    return false;
                }
            }
        }
        $cpdbkbs = $orderrecord->getCpdbkbs()->with('item')->all();
        $transaction = $connection->beginTransaction();
        try {
            foreach ($cpdbkbs as $cpdbkb) {
                $itemid = $cpdbkb->item_id;
                if (isset($calckbarr[$itemid]['kvalue1'])
                    && isset($calckbarr[$itemid]['kvalue1'])
                    && isset($calckbarr[$itemid]['rvalue1'])
                ) {
                    $k1 = $calckbarr[$itemid]['kvalue1'];
                    $b1 = $calckbarr[$itemid]['kvalue1'];
                    $kstr = $cpdbkb->kvalue0 * $k1;
                    $bstr = $cpdbkb->bvalue0 * $k1+$b1;
                    if ($itemid === 22) {
                        $kstr = $kstr/1.5;
                        $bstr = $bstr/1.5;
                    }
                    $kstr = round($kstr, $cpdbkb->item->kround * -1);
                    $bstr = round($bstr, $cpdbkb->item->bround * -1);
                    $kstr = strval($kstr);
                    $bstr = strval($bstr);

                    $cpdbkb->kvalue1 = $k1;
                    $cpdbkb->bvalue1 = $b1;
                    $cpdbkb->rvalue1 = $calckbarr[$itemid]['rvalue1'];
                    $cpdbkb->kstr = $kstr;
                    $cpdbkb->bstr = $bstr;
                    $cpdbkb->avercal2 = isset($avercal2arr[$itemid]) ? $avercal2arr[$itemid] : null ;
                    // echo "string";
                    // var_dump($cpdbkb);
                }
                if (!$cpdbkb->save()) {
                    $msg = 'kb save error';
                    throw new \Exception($msg,1);
                }
            }
            $transaction->commit();
            return true;
        }catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }

    public function updateKB($cpdbkbarr = [], $orderid = '')
    {
        $connection = Yii::$app->db;
        if (empty($orderid) || empty($cpdbkbarr)) {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'empty orderid OR cpdbkb';
            return false;
        }
        $order = Cpdborder::findOne($orderid);
        if (!$order) {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'order no exist';
            return false;
        }
        $cpdbkbs = $order->getCpdbkbs()->All();
        $updatecnt = 0;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($cpdbkbs as $cpdbkb) {
                $itemid = $cpdbkb->item_id;
                if (isset($cpdbkbarr[$itemid]) 
                    && isset($cpdbkbarr[$itemid]['k'])
                    && isset($cpdbkbarr[$itemid]['b'])
                    && isset($cpdbkbarr[$itemid]['lot'])
                    && is_numeric($cpdbkbarr[$itemid]['k'])
                ) {
                    $cpdbkb->lot = $cpdbkbarr[$itemid]['lot'];
                    $cpdbkb->kvalue0 = $cpdbkbarr[$itemid]['k'];
                    $cpdbkb->bvalue0 = $cpdbkbarr[$itemid]['b'];
                    if ($cpdbkb->save()) {
                        $updatecnt++;
                    }
                }
            }
            $transaction->commit();
            return $updatecnt;
        } catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }
    public function readResults($orderid = '')
    {
       $order = Cpdborder::findOne($orderid);
        if (!$order) {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'order not exist';
            return false;
        }
        if(ord($order->panel->type) != ord($order->machine->type)){
            $this->errCode = ErrCode::INNER_ERR;
            $this->errMsg = 'type wrong';
            return false;
        }
        $date = date('Ymd');
        $panelitemarr = [];
        foreach ($order->panel->panelitems as $panelitem) {
            $itemid = $panelitem->item_id;
            $itemname = $panelitem->item->name;
            $panelitemarr[$itemid] = $itemname;
            if ($panelitem->hastwo) {
                $panelitemarr[$itemid+1] = $itemname.'(2)';
            }
        }
/*
        $panelitemarr = $order->getItems()
            ->select([ 'name', 'id' ])
            ->indexBy('id')
            ->column();
        foreach ($panelitemarr as $itemid => $itemname) {
            if (in_array($itemid, [16,18])) {
                $panelitemarr[$itemid+1] = $itemname.'(2)';
            }
        }*/
        if (strtoupper(substr(PHP_OS,0,3)) === 'WIN') {
            //windows
            $path = $order->machine->path .':/';
        }
        else {
            //linux
            $path = '/mnt/' . $order->machine->path . '/';
        }
        switch ($order->panel->type) {
            case 'CM':
                $path .= 'M1成品定标/Result/';
                break;
            case 'PM':
                $path .= 'calibrateM/Result/';
                break;
            case 'CV':
                $path .= 'V1成品定标/Result/';
                break;
               case 'PV':
                $path .= 'calibrateV/Result/';
                # code...
                break;
            default:
                # code...
                break;
        }
        //////////////////////////////////
        // $path = '/home/shan/';
        if (!file_exists($path)) {
            $this->errCode = ErrCode::INNER_ERR;
            $this->errMsg = 'path no exist';
            return false;
        }
        $readarr = readresultfile($path, $order->panel->name ,$date ,$panelitemarr);
                ////////////////////////////
        // var_dump($readarr);
        // exit;
        if (!$readarr['ok']) {
            $this->errCode = ErrCode::FUNC_RET_FALSE;
            $this->errMsg = $readarr['msg'];
            return false;
        }
        $hastwoarr = Panelitem::find()
            ->select(['(item_id)+1'])
            ->where(['hastwo' => 'Y'])
            ->distinct()
            ->column();
        foreach ($readarr['ret'] as $time => $value) {
            foreach ($value as $itemid => $result) {
                if (in_array($itemid, $hastwoarr)) {
                    if (!empty($result)) {
                        $readarr['ret'][$time][$itemid-1] = ($result + $value[$itemid-1]) / 2;
                    }
                    unset($readarr['ret'][$time][$itemid]);
                }
            }
        }
/*        var_dump($readarr['ret']);
        exit;*/
        $lastdatetime = Paneltest::find()
            ->select('MAX(UNIX_TIMESTAMP(chkdatetime))')
            ->where([ 'machine_id' => $order->machine_id ])
            ->scalar();
        $successcnt=0;
        $connection=Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($readarr['ret'] as $key => $value) {
                if($lastdatetime < $key){
                    // echo "record no exist";
                    $paneltest = new Paneltest();
                    $paneltest->chkdatetime = date('Y-m-d H:i:s',$key);
                    $paneltest->machine_id = $order->machine_id;
                    $paneltest->panel_id = $order->panel_id;
                    $paneltest->type = 'CPDB';
                    if (!$paneltest->save()) {
                        throw new \Exception("panel no save ", 1);
                    }
                    $successcnt++;
                    $testid = $connection->getLastInsertId();
                    foreach ($value as $key1 => $value1) {
                            $panelresult = new panelresult();
                            $panelresult->paneltest_id = $testid;
                            $panelresult->item_id = $key1;
                            $panelresult->result = floatval($value1);
                            $panelresult->abandon = 'N';
                        if (!$panelresult->save()) {
                            throw new \Exception("insert result error ", 1);
                        }
                    }
                }
            }
            $transaction->commit();
            return $successcnt;
        } catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg =$e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }
    public function resetResult($orderid = '')
    {
        $order = Cpdborder::findOne($orderid);
        if (empty($orderid) || !$order) {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'order not exist';
            return false;
        }
        $targetarr = [];
        foreach (Itemcali::find()->all() as $itemcali) {
            $targetarr[$itemcali->calibrator_id][$itemcali->item_id] = $itemcali->target;
        }
        // var_dump($targetarr);
        // exit;
        $connection=Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            foreach ($order->cpdbgroups as $cpdbgroup) {
                $caliid = $cpdbgroup->calibrator_id;
                foreach ($cpdbgroup->paneltests as $paneltest) {
                    foreach ($paneltest->panelresults as $panelresult) {
                        $panelresult->abandon = 'N';
                        $panelresult->save();
                    }
                    $paneltest->groupid = null;
                    $paneltest->save();
                }
                foreach ($cpdbgroup->cpdbcalcs as $cpdbcalc) {

                    $itemid = $cpdbcalc->item_id;
                    $cpdbcalc->xvalue = null;
                    $cpdbcalc->xsum = null;
                    $cpdbcalc->xcount = null;
                    $cpdbcalc->bvalue = null;
                    $cpdbcalc->target = $targetarr[$caliid][$itemid];
                    $cpdbcalc->save();
                }
                $cpdbgroup->calibratorlot = null;
                $cpdbgroup->stat = $cpdbgroup->inistat;
                $cpdbgroup->save();
            }
            foreach ($order->cpdbkbs as $cpdbkb) {
                $cpdbkb->lot = null;
                $cpdbkb->kvalue0 = null;
                $cpdbkb->bvalue0 = null;
                $cpdbkb->kvalue1 = null;
                $cpdbkb->bvalue1 = null;
                $cpdbkb->rvalue1 = null;
                $cpdbkb->kstr = null;
                $cpdbkb->bstr = null;
                $cpdbkb->avercal2 = null;
                $cpdbkb->save();
            }
            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg =$e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }
    public function showCaliNames()
    {
        $calinamearr = Calibrator::find()
            ->select([
                'calibrator.name',
                'calibrator.id',
            ])
            ->indexBy('id')
            ->column();
        if (empty($calinamearr)) {
            $this->errCode = ErrCode::INNER_ERR;
            $this->errMsg = 'empty cali';
            return false;
        }
        return $calinamearr;

    }
    public function showTargets()
    {
        $targetitemarr = [];
        foreach (Item::find()->with('itemcalis')->all() as $item) {
            $targetitemarr[$item->id]['name'] = $item->name;
            foreach ($item->itemcalis as $itemcali) {
                $targetitemarr[$item->id][$itemcali->calibrator_id] = $itemcali->target;
            }
        }
        return $targetitemarr;
    }
    public function changeTarget($changetargetarr = [])
    {
        $connection=Yii::$app->db;
        if (empty($changetargetarr)) {
            $this->errCode = ErrCode::PARAM_ERR;
            $this->errMsg = 'empty targetarr';
            return false;
        }
        $transaction = $connection->beginTransaction();
        $changecnt = 0;
        try {
            foreach ($changetargetarr as $itemid => $value) {
                foreach ($value as $caliid => $target) {
                    if(!is_numeric($target)){
                        $msg='target error';
                        throw new \Exception($msg, 1);
                    }
                    $itemcali = Itemcali::findOne(['item_id'=>$itemid, 'calibrator_id'=>$caliid]);
                    if ($itemcali) {
                        $itemcali->target = $target;
                        if ($itemcali->save()) {
                            $changecnt++;
                        }
                    }
                }
            }
            $transaction->commit();
            return $changecnt;
        } catch (\Exception $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg = $e->getMessage();
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $this->errCode = ErrCode::DATABASE_ERR;
            $this->errMsg =$e->getMessage();
            $transaction->rollBack();
            return false;
        }
    }
}
function readresultfile($basepath='',$panel='',$date='',$itemarr=[]){
    $filepath=$basepath.$panel.'#'.$date.'.txt';
    if(!file_exists($filepath)){
        return [
            'ok'=>false,
            'msg'=>'file no exist',
        ];
    }
    $retarray=[];
    $startstr='Test started at ';
    $tempitemarr=[];
    $file = fopen($filepath,"r");
    while(! feof($file)){
        $line=fgets($file);
        if(strstr($line, $startstr)){
            $timestr=substr($line, strpos($line, $startstr)+strlen($startstr),6);
            $datetime=date_create_from_format('YmdHis',$date.$timestr);
            if(!$datetime){
                return [
                    'ok'=>false,
                    'msg'=>'wrong date',
                ];
            }
            $timekey=$datetime->getTimestamp();
            $tempitemarr= $itemarr;
        }
        else{
            foreach ($tempitemarr as $key=> $value) {
                $findstr=$value.': ';
                if(strstr($line, $findstr)){
                    $resultstr=trim(substr($line, strpos($line, $findstr)+strlen($findstr)));
                    if(!is_numeric($resultstr)){
                        return [
                            'ok'=>false,
                            'msg'=>'wrong value',
                        ];
                    }
                    $retarray[$timekey][$key]=floatval($resultstr);
                    unset($tempitemarr[$key]);
                }
            }
        }
    }
    fclose($file);
    return [
        'ok'=>true,
        'ret'=>$retarray,
    ];
}
function calckbr2($x=array(),$y=array()){
    $n=count($x);
    if($n!=count($y)){
        return array('calc' =>0,'msg'=>'x y count not same ');
    }
    if($n<2) {
        return array('calc' =>0,'msg'=>' count less 2 ');
    }
    if(count(array_unique($x))<$n) {
        return array('calc' =>0,'msg'=>'x value same ');
    }
    $xsum=$ysum=$y2sum=$x2sum=$xysum=0;
    for($i=0;$i<$n;$i++) {
        $xsum+=$x[$i];
        $x2sum+=$x[$i]*$x[$i];
        $ysum+=$y[$i];
        $y2sum+=$y[$i]*$y[$i];
        $xysum+=$y[$i]*$x[$i];
    }
    $tmp=$n*$xysum-$xsum*$ysum;
    $tmpdiv1=$n*$x2sum-$xsum*$xsum;
    if($tmpdiv1){
        $kval=$tmp/$tmpdiv1;
        $bval=($ysum-$kval*$xsum)/$n;
        $tmpdiv1=$n*$y2sum-$ysum*$ysum;
        if($n==2){
            $r2val=1;
        }
        elseif($tmpdiv1){
            $r2val=$tmp/$tmpdiv1*$kval;
        }
        else{
            $r2val=1;    
        }
        $dyval=sqrt(($y2sum*$n-$ysum*$ysum))/$n;
        return array('calc' =>1,'k' =>$kval,'b' =>$bval,'r2' =>$r2val,'dy'=>$dyval,'count' =>$n,);
    }
    else{
        return array('calc' =>0,'msg'=>array($x,$y));
    }
}
function chksubval($array = [], $target = '', $point = 2)
{
    if (!is_numeric($target) || count($array) != $point) {
        return false;
    }
    $errcnt = array_fill(0, $point, 0);
    $array = array_values($array);
    if ($point == 2) {
        if (abs($array[0] - $array[1]) >= $target) {
            return 1;
        } else {
            return 0;
        }
    } elseif ($point == 3) {
        $sub12 = abs($array[0] - $array[1]) < $target;
        $sub13 = abs($array[0] - $array[2]) < $target;
        $sub23 = abs($array[2] - $array[1]) < $target;
        $total = $sub12 + $sub13 + $sub23;
        switch ($total) {
            case 0:
                return $point + 1;
                break;
            case 1:
                return $sub12 ? 3 : ($sub13 ? 2 : 1) ;
                break;
            case 2:
                return false;
                break;
            case 3:
                return false;
                break;
            default:
                return false;
                break;
        }
    } else {
        return false;
    }
}