<?php

define('PUN_CENSOR_LOADED', 1);

$search_for = array (
  0 => '%(?<=[^\\p{L}\\p{N}])([\\p{L}\\p{N}]*?fuck[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  1 => '%(?<=[^\\p{L}\\p{N}])([\\p{L}\\p{N}]*?shit[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  2 => '%(?<=[^\\p{L}\\p{N}])(ass)(?=[^\\p{L}\\p{N}])%iu',
  3 => '%(?<=[^\\p{L}\\p{N}])([\\p{L}\\p{N}]*?bitch[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  4 => '%(?<=[^\\p{L}\\p{N}])(assed)(?=[^\\p{L}\\p{N}])%iu',
  5 => '%(?<=[^\\p{L}\\p{N}])(asses)(?=[^\\p{L}\\p{N}])%iu',
  6 => '%(?<=[^\\p{L}\\p{N}])([\\p{L}\\p{N}]*?asshole[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  7 => '%(?<=[^\\p{L}\\p{N}])([\\p{L}\\p{N}]*?piss[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  8 => '%(?<=[^\\p{L}\\p{N}])([\\p{L}\\p{N}]*?twat[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
  9 => '%(?<=[^\\p{L}\\p{N}])(fag[\\p{L}\\p{N}]*?)(?=[^\\p{L}\\p{N}])%iu',
);

$replace_with = array (
  0 => '*bleep*',
  1 => '*bleep*',
  2 => '*bleep*',
  3 => '*bleep*',
  4 => '*bleep*ed',
  5 => '*bleep*s',
  6 => '*bleep*',
  7 => '*bleep*',
  8 => '*bleep*',
  9 => '*bleep*',
);

?>