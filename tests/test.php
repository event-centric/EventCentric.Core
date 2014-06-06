<?php
require_once __DIR__.'/../vendor/autoload.php';

// https://gist.github.com/mathiasverraes/9046427
function it($m,$p){echo ($p?'✔︎':'✘')." It $m\n"; if(!$p){$GLOBALS['f']=1;}}function done(){if(@$GLOBALS['f'])die(1);}
function all(array $ps){return array_reduce($ps,function($a,$p){return $a&&$p;},true);}

include __DIR__ . '/EventCentric/Protection/Tests/AggregateRoot/DomainTest.php';
include __DIR__ . '/EventCentric/Protection/Tests/AggregateRoot/EventSourcingTest.php';
include __DIR__ . '/EventCentric/Protection/Tests/AggregateRoot/ReconstitutionTest.php';
done();