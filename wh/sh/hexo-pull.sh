#!/usr/bin/env bash
#主站博客自动部署脚本

export NODE_HOME=/usr/local/node
export PATH=$NODE_HOME/bin:$PATH

pwd='/data/html/hexo'

cd $pwd/source
git pull

cd $pwd
$pwd/node_modules/hexo/bin/hexo g
