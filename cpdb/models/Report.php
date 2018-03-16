<?php
namespace app\models;

class Report
{
    const ROW_ITEM = 'ROW_ITEM';
    const ROW_UNIT = 'ROW_UNIT';
    const ROW_HOLE = 'ROW_HOLE';
    const ROW_LOT = 'ROW_LOT';
    const ROW_KB0 = 'ROW_KB0';
    const ROW_AVER = 'ROW_AVER';
    const ROW_TARGET = 'ROW_TARGET';
    const ROW_KB = 'ROW_KB';
    const ROW_R2 = 'ROW_R2';
    // const ROW_B = 'ROW_B';
    const ROWPOS_TESTSTART1 = 'ROWPOS_TESTSTART1';
    const ROWPOS_TESTSTART2 = 'ROWPOS_TESTSTART2';
    const ROWPOS_TESTSTART3 = 'ROWPOS_TESTSTART3';
    // const ROWPOS_TESTEND1 = 'ROWPOS_TESTEND1';
    // const ROWPOS_TESTEND2 = 'ROWPOS_TESTEND2';
    // const ROWPOS_TESTEND3 = 'ROWPOS_TESTEND3';
    const COL_NAME = 'COL_NAME';
    const COL_CALILOT = 'COL_CALILOT';

    public $rowcnt = 0;
    public $colcnt = 0;
    private $rowarray = [];
    private $colarray = [];
    private $reporttable = [];

    private function setTable($row = '', $col = '' ,$value = '')
    {
        $rownumber = is_numeric($row) ? $row : (isset($this->rowarray[$row]) ? $this->rowarray[$row] : null);
        $colnumber = is_numeric($col) ? $col : (isset($this->colarray[$col]) ? $this->colarray[$col] : null);
        if (is_null($rownumber) || is_null($colnumber)) {
            return false;
        } else {
            $this->reporttable[$rownumber][$colnumber] = $value;
            return true;
        }
    }

    public function getTabelData($excel = false)
    {
        return $this->reporttable;
    }

    public function Inidata($input = '')
    { 
        if (!$input || !is_array($input) || !isset($input['reportarr'])) {
            return false;
        }
        $array = $input['reportarr'];

        $tmprowarr = [];
        $i = 0;
        $tmprowarr[$i++] = self::ROW_ITEM;
        $tmprowarr[$i++] = self::ROW_UNIT;
        $tmprowarr[$i++] = self::ROW_HOLE;
        $tmprowarr[$i++] = self::ROW_LOT;
        $tmprowarr[$i++] = self::ROW_KB0;
        $tmprowarr[$i] = self::ROWPOS_TESTSTART1;
        $i += array_sum($array["testcntarr0"]);
        $i += 2 * count($array["testcntarr0"]);
        // $tmprowarr[$i-1] = self::ROWPOS_TESTEND1;
        $tmprowarr[$i] = self::ROWPOS_TESTSTART2;
        $i += array_sum($array["testcntarr1"]);
        $i += count($array["testcntarr1"]);
        // $tmprowarr[$i-1] = self::ROWPOS_TESTEND2;
        $tmprowarr[$i++] = self::ROW_AVER;
        $tmprowarr[$i++] = self::ROW_TARGET;
        $tmprowarr[$i++] = self::ROW_KB;
        $tmprowarr[$i++] = self::ROW_R2;
        $tmprowarr[$i] = self::ROWPOS_TESTSTART3;
        $i += array_sum($array["testcntarr2"]);
        $i += count($array["testcntarr2"]);
        // $tmprowarr[$i-1] = self::ROWPOS_TESTEND3;
        // $tmprowarr[$i++] = self::ROW_B;
        $this->rowarray = array_flip($tmprowarr);
        $this->rowcnt = $i;
        $tmpcolarr = [];
        $j = 0;
        $tmpcolarr[$j++] = self::COL_NAME;
        $j += $array['itemcnt'];
        $tmpcolarr[$j++] = self::COL_CALILOT;
        $this->colarray = array_flip($tmpcolarr);
        $this->colcnt = $j;

        $reporttable = [];
        for ($i=0; $i < $this->rowcnt; $i++) { 
            for ($j=0; $j < $this->colcnt; $j++) { 
                $this->reporttable[$i][$j] = '-';
            }
        }
        $this->setTable(self::ROW_ITEM, self::COL_NAME, 'item');
        $this->setTable(self::ROW_UNIT, self::COL_NAME, 'unit');
        $this->setTable(self::ROW_HOLE, self::COL_NAME, 'hole');
        $this->setTable(self::ROW_LOT, self::COL_NAME, 'lot');
        $this->setTable(self::ROW_KB0, self::COL_NAME, 'kb0');
        $this->setTable(self::ROW_ITEM, self::COL_CALILOT, 'calibratorlot');
        $this->setTable(self::ROW_AVER, self::COL_NAME, 'CAL2 aver');
        $this->setTable(self::ROW_KB, self::COL_NAME, 'kb');
        $this->setTable(self::ROW_R2, self::COL_NAME, 'R2');
        $itempos = [];
        foreach ($array['kbarr'] as $itemid => $value) {
            $col = $value['position'];
            $itempos[$itemid] = $col;
            $this->setTable(self::ROW_ITEM, $col, $value['item']);
            $this->setTable(self::ROW_UNIT, $col, $value['unit']);
            $this->setTable(self::ROW_HOLE, $col, $value['hole']);
            $this->setTable(self::ROW_LOT, $col, $value['lot']);
            $this->setTable(self::ROW_KB0, $col, sprintf('y=%sx+%s', $value['kvalue0'], $value['bvalue0']));
            $this->setTable(self::ROW_AVER, $col, $value['avercal2']);
            $this->setTable(self::ROW_KB, $col, sprintf('y=%sx+%s', $value['kstr'], $value['bstr']));
            $this->setTable(self::ROW_R2, $col, $value['rvalue1']);

        }
        $rowpos0 = $this->rowarray[self::ROWPOS_TESTSTART1];
        $rowpos1 = $this->rowarray[self::ROWPOS_TESTSTART2];
        $rowpos2 = $this->rowarray[self::ROWPOS_TESTSTART3];
        $grouporder = 0;
        foreach ($array['grouparr'] as $groupid => $value) {
            if (!$value['iscal2']) {
                $testorder = 1;
                foreach ($value['resultarr'] as $testid => $value1) {
                    $this->setTable($rowpos0, self::COL_NAME, $value['calibrator'].' - '.$testorder++);
                    foreach ($value1 as $itemid => $value2) {
                        $this->setTable($rowpos0, $itempos[$itemid], $value2['result']);
                    }
                    $this->setTable($rowpos0, self::COL_CALILOT, $value['calibratorlot']);
                    $rowpos0++;
                }
                foreach ($value['averarr'] as $itemid => $value1) {
                    $this->setTable($rowpos0, self::COL_NAME, $value['calibrator'].' - aver');
                    $this->setTable($rowpos0, $itempos[$itemid], $value1['xvalue']);
                    $this->setTable($rowpos0+1, self::COL_NAME, $value['calibrator'].' - target');
                    $this->setTable($rowpos0+1, $itempos[$itemid], $value1['target']);
                }
                $rowpos0 += 2;
            } elseif($value['recheck'] == 'N') {
                $testorder = 1;
                $grouporder++;
                foreach ($value['resultarr'] as $testid => $value1) {
                    $this->setTable($rowpos1, self::COL_NAME, $value['calibrator'].' '.$grouporder.'- '.$testorder++);
                    foreach ($value1 as $itemid => $value2) {
                        $this->setTable($rowpos1, $itempos[$itemid], $value2['result']);
                    }
                    $this->setTable($rowpos1, self::COL_CALILOT, $value['calibratorlot']);
                    $rowpos1++;
                }
                foreach ($value['averarr'] as $itemid => $value1) {
                    $this->setTable($rowpos1, self::COL_NAME, $value['calibrator'].' '.$grouporder.'-B%');
                    $this->setTable($rowpos1, $itempos[$itemid], round(abs($value1['xvalue'] - $value1['target']) / $value1['target'] *100, 2));
                    if (!isset($targetset[$itemid])) {
                        $targetset[$itemid] = true;
                        $this->setTable(self::ROW_TARGET, self::COL_NAME, $value['calibrator'].' -target');
                        $this->setTable(self::ROW_TARGET, $itempos[$itemid], $value1['target']);
                    }
                }
                $rowpos1 ++;
            } else {
                $testorder = 1;
                foreach ($value['resultarr'] as $testid => $value1) {
                    $this->setTable($rowpos2, self::COL_NAME, $value['calibrator'].'  recheck - '.$testorder++);
                    foreach ($value1 as $itemid => $value2) {
                        $this->setTable($rowpos2, $itempos[$itemid], $value2['result']);
                    }
                    $this->setTable($rowpos2, self::COL_CALILOT, $value['calibratorlot']);
                    $rowpos2++;
                }
                foreach ($value['averarr'] as $itemid => $value1) {
                    $this->setTable($rowpos2, self::COL_NAME, $value['calibrator'].' recheck-B%');
                    $this->setTable($rowpos2, $itempos[$itemid], round(abs($value1['xvalue'] - $value1['target']) / $value1['target'] *100, 2));
                    if (!isset($targetset[$itemid])) {
                        $targetset[$itemid] = true;
                        $this->setTable(self::ROW_TARGET, self::COL_NAME, $value['calibrator'].' -target');
                        $this->setTable(self::ROW_TARGET, $itempos[$itemid], $value1['target']);
                    }
                }
                $rowpos2 ++;
            }
        }
        return true;
    }
}