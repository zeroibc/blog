#!/bin bash
#主站博客自动部署脚本

export NODE_HOME=/usr/local/src/node
export PATH=$NODE_HOME/bin:$PATH

pwd='/var/local/www/blog'

cd $pwd
git pull
hexo g
