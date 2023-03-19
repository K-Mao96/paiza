<?php
    //プレイヤークラス
    class Player {
        //技の数
        private const MOVE_COUNT = 3;
        //プレイヤーのHP
        private int $hp;
        //技
        private array $moves = [];
        //退場フラグ（退場したらtrueにする)
        private bool $exitFlg = false;
        //コンストラクタ
        public function __construct(
            int $hp,
            int $frame_1,
            int $power_1,
            int $frame_2,
            int $power_2,
            int $frame_3,
            int $power_3
            ) {
                $this->hp = $hp;

                $frames = [$frame_1, $frame_2, $frame_3];
                $powers = [$power_1, $power_2, $power_3];
                
                //技をインスタンス化する
                for ($moveId = 0; $moveId < self::MOVE_COUNT; $moveId++) {
                    $this->makeMove($moveId, $frames[$moveId], $powers[$moveId]);
                }
            
        }

        //攻撃技・強化技に分けてインスタンス化し、配列に入れる
        public function makeMove($moveId, $frame, $power) {
            $moveId++;
            if ($frame == StrengthenMove::FRAME && $power == StrengthenMove::POWER) {
                $this->moves[$moveId] = new StrengthenMove($frame, $power);
            } else {
                $this->moves[$moveId] = new AttackMove($frame, $power);
            }
        }
        
        //特定の技を返す
        public function getMove(int $moveId) {
            return $this->moves[$moveId];
        }
        
        //退場フラグを返す
        public function getExitFlg() {
            return $this->exitFlg;
        }
        
        //攻撃技を使う
        public function attack(int $moveId, Player $player) {
            $this->getMove($moveId)->attack($player);
        }
        //強化技を使う
        public function strengthen(int $moveId) {
            $this->getMove($moveId)->strengthen($this->moves);
        }
        //攻撃を受ける
        public function damage (int $power) {
            $this->hp -= $power;
            //hpが0になったら退場フラグをtrueにする
            if ($this->hp <= 0) {
                $this->exitFlg = true;
            }
        }
    }
    
    //技クラス
    class Move {
        //発生フレーム
        protected int $frame;
        //攻撃力
        protected int $power;
        
        //コンストラクタ
        public function __construct(int $frame, int $power) {
            $this->frame = $frame;
            $this->power = $power;
        }

        //技の発生フレームを返す
        public function getFrame() {
            return $this->frame;
        }
    }
    
    //強化技クラス
    class StrengthenMove extends Move {
        //強化技の発生フレーム:0
        public const FRAME = 0;
        //強化技の攻撃力:0
        public const POWER = 0;
        //他の技への効果（発生フレーム-3、攻撃力+5。ただし、発生フレームの最小値は1）
        public const EFFECT_FRAME = 3;
        public const EFFECT_POWER = 5;
        
        //他の攻撃技を強化する
        public function strengthen(array $moves) {
            foreach ($moves as $move) {
                if (get_class($move) == 'AttackMove') {
                    $move->strengthenFrame();
                    $move->strengthenPower();
                }
            }
            
        }
    }
    
    //攻撃技クラス
    class AttackMove extends Move {
        
        //相手を攻撃する
        public function attack(Player $player) {
            $player->damage($this->power);
        }
        
        //発生フレームを強化する
        public function strengthenFrame() {
            //発生フレームの最小値は1
            if ($this->frame >= (StrengthenMove::EFFECT_FRAME + 1)) {
                $this->frame -= StrengthenMove::EFFECT_FRAME;
            }
        }
        //攻撃力を強化する
        public function strengthenPower() {
            $this->power += StrengthenMove::EFFECT_POWER;
        }
    }
    
    //プレイヤー数、攻撃回数を取得する
    fscanf(STDIN, "%d %d", $playerCount, $attackCount);
    
    //プレイヤーを管理する配列
    $players = [];
    //プレイヤー数だけ繰り返す
    for ($playerId = 1; $playerId <= $playerCount; $playerId ++) {
        //プレイヤーのHp、技1の発生フレーム、技１の攻撃力、技２の発生フレーム、技２の攻撃力、技３の発生フレーム、技３の攻撃力を取得する
        fscanf(STDIN, "%d %d %d %d %d %d %d", $hp, $frame_1, $power_1, $frame_2, $power_2, $frame_3, $power_3);
        //すべてのプレイヤーをインスタンス化する
        $player = new Player($hp, $frame_1, $power_1, $frame_2, $power_2, $frame_3, $power_3);
        $players[$playerId] = $player;
    }
    
    //攻撃回数だけ繰り返す
    for ($i = 1; $i <= $attackCount; $i++) {
        //技を使ったプレイヤー番号、そのプレイヤーが選んだ技番号、対戦相手のプレイヤー番号、そのプレイヤーが選んだ技番号を取得する
        fscanf(STDIN, "%d %d %d %d", $playerId_1, $moveId_1, $playerId_2, $moveId_2);
        //一人目のプレイヤー
        $player_1 = $players[$playerId_1];
        //二人目のプレイヤー
        $player_2 = $players[$playerId_2];
        //プレイヤーのうち少なくとも片方が退場していたら処理を抜ける
        if ($player_1->getExitFlg() || $player_2->getExitFlg()) {
            continue;
        }
        
        //どちらも攻撃系の技を使った場合
        if (get_class($player_1->getMove($moveId_1)) == 'AttackMove' && get_class($player_2->getMove($moveId_2)) == 'AttackMove') {
            //フレームが短い方の技を発動する（フレームが同値なら何もしない）
            if ($player_1->getMove($moveId_1)->getFrame() < $player_2->getMove($moveId_2)->getFrame()) {
                if (get_class($player_1->getMove($moveId_1)) == 'AttackMove') {
                    $player_1->attack($moveId_1, $player_2);
                }
                
            } else {
                if (get_class($player_2->getMove($moveId_2)) == 'AttackMove') {
                    $player_2->attack($moveId_2, $player_1);
                }
            }
        } elseif (get_class($player_1->getMove($moveId_1)) == 'StrengthenMove' && get_class($player_2->getMove($moveId_2)) == 'StrengthenMove') {
            $player_1->strengthen($moveId_1);
            $player_2->strengthen($moveId_2);
        } elseif (get_class($player_1->getMove($moveId_1)) == 'StrengthenMove') {
            $player_1->strengthen($moveId_1);
            $player_2->attack($moveId_2, $player_1);
        } elseif (get_class($player_2->getMove($moveId_2)) == 'StrengthenMove') {
            $player_2->strengthen($moveId_2);
            $player_1->attack($moveId_1, $player_2);
        }
        
        
    }

    
    //全てのプレイヤーのうち、退場フラグがfalseのプレイヤーをカウントし、その数を出力する
    $survivorCount = 0;
    foreach ($players as $player) {
        $exitFlg = $player->getExitFlg();
        if (!$exitFlg) {
            $survivorCount++;
        }
    }
    
    echo $survivorCount;
    
?>