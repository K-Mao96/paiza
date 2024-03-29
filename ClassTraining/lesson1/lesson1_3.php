<?php
/**
 * piza「クラス・構造体メニュー」
 * 構造体の更新
 * STEP: 3 構造体の整列
 * @link https://paiza.jp/works/mondai/class_primer/class_primer__sort/edit?language_uid=php
 */



/**生徒クラス */
class Student {

    //生徒の基本情報
    public string $name; //氏名
    public int    $old;  //年齢
    public string $birth;//生月日
    public string $state;//出身地

    /**
     * 基本情報を初期化する
     * @param array $input {
     *  name:  string,
     *  old:   int,
     *  birth: string,
     *  state: string
     * }
     */
    public function __construct(array $input) {
        list($this->name, $this->old, $this->birth, $this->state) = $input;
    }
}




//生徒の人数(N)を取得
$totalStudent = trim(fgets(STDIN));

//生徒のインスタンスを管理する配列を定義
$studentList = [];

//生徒の数だけインスタンスを作り、studentListに格納
for ($i = 1; $i <= $totalStudent; $i++) {
    //生徒1人分の情報を取得
    $input = trim(fgets(STDIN));
    //配列に変換する
    $input = explode(" ", $input);
    array_push($studentList, new Student($input));
}

//oldが若い順にstudentListの要素を並べ替える
$old  = array_column($studentList, 'old');
array_multisort($old, SORT_ASC, $studentList);

//並べ替えた順で出力する
foreach ($studentList as $student) {
    echo $student->name, " ", $student->old, " ", $student->birth, " ", $student->state, "\n";
}
?>