#!/usr/bin/env bash
#主站博客自动部署脚本

pwd='/var/local/www/blog'
pkill php

cd $pwd
git pull

hexo g

cd $pwd/wh
nohup php -S 0.0.0.0:8080 ./git.php
