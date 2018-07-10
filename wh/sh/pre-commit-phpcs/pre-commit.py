# coding: utf8
import sys
import os
import platform

def phpcs(ui, repo, hooktype, node=None, source=None, **kwargs):
    cmd_git = "git diff --name-only"
    csignore_name = ".csignore"
    args = "-n -s"
    php_files = csignore_files = ''
    php_files_count = 0
    # 所有变更的文件
    for item in os.popen(cmd_git).readlines() :
        item = item.strip()
        if item.find("php") == -1 or not os.path.isfile(item) :
            continue
        # php语法检测
        php_syntax = os.popen("php -l %s" % (item)).read()
        if php_syntax.find("No syntax errors") == -1 :
            ui.warn(php_syntax)
        # 待phpcs检测的文件
        php_files = php_files + " " + item
        php_files_count = php_files_count + 1
    # 忽略文件
    if os.path.isfile(csignore_name) :
        for item in open(csignore_name).readlines() :
            csignore_files = csignore_files + "," + item.strip()
    csignore_args = "--ignore='%s'" % (csignore_files.strip(','))
    if php_files_count > 0 :
        cmd_phpcs = "phpcs %s %s %s" % (args, csignore_args, php_files)
        msg = os.popen(cmd_phpcs).read().strip('.\r\n')
        if msg != '' :
            ui.warn(msg + "\r\n")
            return True
    return False