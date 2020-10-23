#!/usr/bin/env bash
wget https://github.com/swow/swow/archive/"${SW_VERSION}".tar.gz -O swow.tar.gz
mkdir -p swow
tar -xf swow.tar.gz -C swow --strip-components=1
rm swow.tar.gz
cd swow/ext || exit

phpize
./configure --enable-debug
make -j "$(nproc)"
sudo make install
