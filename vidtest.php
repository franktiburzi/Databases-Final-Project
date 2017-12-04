<?php

try {
  $media = mediafile\bin\mediafile::open('C:\Users\Frank\Pictures\Fall-Colors-Tokyo-2011-G8975.jpg');
  // for audio
  if ($media->isAudio()) {
    // calls to AudioAdapter interface
    echo 'Duration: '.$media->getAudio()->getLength().PHP_EOL;
    echo 'Bit rate: '.$media->getAudio()->getBitRate().PHP_EOL;
    echo 'Sample rate: '.$media->getAudio()->getSampleRate().PHP_EOL;
    echo 'Channels: '.$media->getAudio()->getChannels().PHP_EOL;
  }
  // for video
  else {
    // calls to VideoAdapter interface
    echo 'Duration: '.$media->getVideo()->getLength().PHP_EOL;
    echo 'Dimensions: '.$media->getVideo()->getWidth().'x'.$media->getVideo()->getHeight().PHP_EOL;
    echo 'Framerate: '.$media->getVideo()->getFramerate().PHP_EOL;
  }
} catch (wapmorgan\MediaFile\Exception $e) {
  // not a media or file is corrupted
}

 ?>
