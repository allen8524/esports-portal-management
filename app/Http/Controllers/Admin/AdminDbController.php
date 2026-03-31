<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminDbController extends Controller
{
    // MySQL 기준 모든 테이블 이름 가져오기
    protected function getTables(): array
    {
        $rows = DB::select('SHOW TABLES');

        $tables = [];
        foreach ($rows as $row) {
            $rowArray = (array) $row;     // ← 먼저 변수에 담고
            $tables[] = reset($rowArray); // ← 그 변수를 reset()에 넘긴다
        }

        sort($tables);

        return $tables;
    }


    // 테이블 목록 페이지
    public function index()
    {
        $tables = $this->getTables();

        return view('admin.db.index', compact('tables'));
    }

    // 특정 테이블 데이터 보기
    public function show(string $table)
    {
        $tables = $this->getTables();

        if (!in_array($table, $tables, true)) {
            abort(404);
        }

        $columns = Schema::getColumnListing($table);

        $query = DB::table($table);

        if (!empty($columns)) {
            $query->orderByDesc($columns[0]); // 보통 id
        }

        $rows = $query->paginate(20);

        return view('admin.db.show', [
            'table' => $table,
            'columns' => $columns,
            'rows' => $rows,
        ]);
    }
}
