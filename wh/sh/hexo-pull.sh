#!/bin bash
#主站博客自动部署脚本

pwd='/var/local/www/blog'

cd $pwd
git pull
hexo g
