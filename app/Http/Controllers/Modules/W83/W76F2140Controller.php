<?php

namespace App\Http\Controllers\Modules\W83;

use App\Http\Controllers\Controller;
use App\Models\D76T2140;
use App\Models\D76T2141;
use Helpers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class  W76F2140Controller extends Controller
{
    public function __construct(D76T2140 $d76T2140, D76T2141 $d76T2141)
    {
        $this->d76T2140 = $d76T2140;
        $this->d76T2141 = $d76T2141;
    }

    public function index(Request $request, $task = "")
    {
        $title = 'Quản lý bản tin';

        $permission = Helpers::getPermission('W76F2140');

        switch ($task) {
            case '':
                $newsCollection = $this->getList();
                $newsCollection = json_encode($newsCollection);
                return view("modules/W83/W76F2140/W76F2140", compact('title', 'newsCollection','permission'));
                break;
            case 'filter':
                $dataPost = $request->input();
                $titleSearch = $dataPost["searchTitle"];
                $newsCollection = $this->getFilterList($titleSearch);
                return view("modules/W83/W76F2140/W76F2140" , compact('title','newsCollection'));
                break;
            case "delete":
                try {
                    $newsID = $request->input('newsID', '');
                    if ($this->delete($newsID)) {
                        \Helpers::setSession('successMessage', \Helpers::getRS('Du_lieu_da_duoc_xoa_thanh_cong'));
                        return json_encode(['status' => 'SUCC', 'message' => \Helpers::getRS('Du_lieu_da_duoc_xoa_thanh_cong')]);
                    } else {
                        return json_encode(array('status' => 'ERROR', 'message' => \Helpers::getRS("Co_loi_xay_ra_trong_qua_trinh_xoa_du_lieu")));
                    }
                } catch (\Exception $ex) {
                    \Helpers::log($ex->getMessage());
                    return json_encode(['status' => 'ERROR', 'message' => $ex->getMessage()]);
                }
                break;
        }

    }

    public function delete($newsID)
    {
        $result = $this->d76T2140->where('NewsID', "=", $newsID)->delete();
        return $result;
    }


    public function getList()
    {
        $userID = Auth::user()->UserID;
        $sql = '--Do nguon cho luoi'.PHP_EOL;
        $sql .= "EXEC W76P2140 '$userID'";
        $collection = DB::select($sql);
        //\Debugbar::info($collection);
        foreach ($collection as &$item) {
            //unset($item->Image);
            $item->Image = htmlentities($item->Image);
        }
        return $collection ;
    }

    public function getFilterList($title)
    {
        $collection = $this->d76T2140->where('Title', 'like', $title);
        return $collection;
    }
}
