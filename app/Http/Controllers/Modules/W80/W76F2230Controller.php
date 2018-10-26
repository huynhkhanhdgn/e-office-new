<?php

namespace App\Http\Controllers\Modules\W80;

use App\Eoffice\Helper;
use App\Http\Controllers\Controller;
use App\Models\D76T0000;
use App\Models\D76T2200;
use App\Models\D76T2230;
use App\Models\D76T9000;
use Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class  W76F2230Controller extends Controller
{
    private $d76T2200;
    private $d76T9000;
    private $d76T2230;

    public function __construct(D76T2200 $d76T2200, D76T9000 $d76T9000, D76T2230 $d76T2230)
    {
        $this->d76T2200 = $d76T2200;
        $this->d76T9000 = $d76T9000;
        $this->d76T2230 = $d76T2230;
        $this->newsHelper = new \App\Module\News\Helper();
    }

    public function index($task = "", $DivisionID = "", Request $request)
    {
        $title= Helpers::getRS('Quan_ly_phong_hop');
        switch ($task) {
            case '':
            case 'listRoom':
                $userID = Auth::user()->UserID;
                $divisionID = $DivisionID == "" ? session('W76P0000')->DivisionID : $DivisionID;
                $orgUnitID = session('W76P0000')->OrgUnitID;

                //$divisionIDList = $this->d76T9000->where('DISABLED', '=', 0)->select('OrgunitID', 'OrgunitName')->get();
                $sql = '--Do nguon cho combo don vi' . PHP_EOL;
                $sql .= "EXEC D76P9000 '$userID', '$orgUnitID','$divisionID'";
                $divisionIDList = DB::select($sql);
                //Lay danh sach news cho channel
//                $newsList = $this->getCalender($divisionIDList);
                //\Debugbar::info($divisionIDList);
                $isblackBoardName = 'Bảng ghi,';
                $isProjectorName = 'Máy chiếu,';
                $isEthernetName = 'Ethernet,';
                $isPCName = 'PC,';
                $isMicrophoneName = 'Microphone,';
                $isTeleConName = 'TeleCon,';
                $isWifiName = 'Wifi';

                $meetingRoomList = $this->d76T2200
                    ->select('FacilityNo as id', 'FacilityName as title', 'DisplayOrder as display',
                        "*",
                        DB::raw("(CASE WHEN IsBlackboard = 1 THEN N'$isblackBoardName' ELSE '' END) AS IsBlackboardName"),
                        DB::raw("(CASE WHEN IsProjector = 1 THEN N'$isProjectorName' ELSE '' END) AS IsProjectorName"),
                        DB::raw("(CASE WHEN IsEthernet = 1 THEN N'$isEthernetName' ELSE '' END) AS IsEthernetName"),
                        DB::raw("(CASE WHEN IsPC = 1 THEN N'$isPCName' ELSE '' END) AS IsPCName"),
                        DB::raw("(CASE WHEN IsMicrophone = 1 THEN N'$isMicrophoneName' ELSE '' END) AS IsMicrophoneName"),
                        DB::raw("(CASE WHEN IsTeleCon = 1 THEN N'$isTeleConName' ELSE '' END) AS IsTeleConName"),
                        DB::raw("(CASE WHEN IsWifi = 1 THEN N'$isWifiName' ELSE '' END) AS IsWifiName")
                    )
                    ->where("DivisionID", '=', session('W76P0000')->DivisionID)->orderBy('DisplayOrder', 'FacilityNo')->get();
                $Logistics = DB::table('D76T1556')
                    ->where('ListTypeID', 'D76T2200_Logistics')
                    ->select('CodeID', 'CodeName')
                    ->get()->toArray();
                foreach ($meetingRoomList as &$item) {
                    $l = explode(';', $item->Logistics);
                    $tmp = array_filter($Logistics, function ($el) use ($l) {
                        return in_array($el->CodeID, $l);
                    });
//                    $tmp = DB::table('D76T1556')
//                        ->where('ListTypeID', 'D76T2200_Logistics')
//                        ->whereIn('CodeID', $Logistics)
//                        ->select('CodeName')
//                        ->get()->toArray();
                    $rs = [];
                    foreach ($tmp as $t) {
                        array_push($rs, $t->CodeName);
                    }
                    $item->LogisticsName = $rs;
                }

                //$hostPersonDetail = $this->d76T2230->select('EmployeeCode', 'Fullname')->get();
                //\Debubar::info($hostPersonDetail);
                //var_dump($meetingRoomList);die();
                $meetingRoomList = json_encode($meetingRoomList);
                //\Debugbar::info($meetingRoomList);
                $divisionIDList = json_encode($divisionIDList);

                //$meetingRoomDetail = $this->d76T2200->where("DivisionID", '=', session('W76P0000')->DivisionID)->get();
                //\Debugbar::info($meetingRoomDetail);
                $newsCollection = $this->getCalender();
                $newsCollection = ($newsCollection);
                \Debugbar::info($newsCollection);

                $limitTime = D76T0000::first();
                if (isset($limitTime) && !empty($limitTime)) {
                    $limitTime->BookingTimeFrom = date('H:i:s', strtotime($limitTime->BookingTimeFrom));
                    $limitTime->BookingTimeTo = date('H:i:s', strtotime($limitTime->BookingTimeTo));
                }

                return view("modules/W80/W76F2230/W76F2230", compact('title','newsList','hostPersonDetail', 'meetingRoomDetail', 'newsCollection', 'rowData', 'divisionIDList', 'meetingRoomList', 'task', 'limitTime', 'divisionID'));
                break;
            case 'loadCalender':
                $newsCollection = $this->getCalender();
                //\Debugbar::info($newsCollection);
                return $newsCollection();
                break;
            case 'delete':
                $ID = $request->input('ID', '');
                //\Debugbar::info($ID);
                $sql = "--Xoa phong hop" . PHP_EOL;
                $sql .= "delete from D76T2230 where ID = '$ID'" . PHP_EOL;
                //\Debugbar::info($sql);
                try {
                    DB::statement($sql);
                    Helpers::setSession('successMessage', Helpers::getRS('Du_lieu_da_duoc_xoa_thanh_cong'));
                    return json_encode(['status' => 'SUCC', 'message' => Helpers::getRS('Du_lieu_da_duoc_xoa_thanh_cong')]);
                } catch (\Exception $ex) {
                    Helpers::log($ex->getMessage());
                    return json_encode(['status' => 'ERROR', 'message' => $ex->getMessage()]);
                }
                break;
        }
    }

    public function getCalender()
    {
        $userID = Auth::user()->UserID;
        $divisionID = session('W76P0000')->DivisionID;
        $orgUnitID = session('W76P0000')->OrgUnitID;
        $sql = '--Do nguon cho luoi' . PHP_EOL;
        $sql .= "EXEC W76P2230 '$userID', '$divisionID','$orgUnitID'";
        $collection = DB::select($sql);
        foreach ($collection as $row) {
            $row->resourceId = $row->FacilityID;
            $row->title = $row->Description;
            $RequestedDateFrom = $row->RequestedDateFrom;
            $RequestedDateTo = $row->RequestedDateTo;
            $row->start = str_replace(' ', 'T', $RequestedDateFrom);
            $row->end = str_replace(' ', 'T', $RequestedDateTo);
        }
        //\Debugbar::info($collection);
        return json_encode($collection);
    }

    public function deleteCalender($ID)
    {
        $result = $this->d76T2230->where("ID", "=", $ID)->delete();
        //\Debugbar::info($result);
        return $result;
    }

}
