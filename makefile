# 同步某分支，理论目前只需同步master即可，除非大模块基于某个项目分支同时多人开发
sync-%:
	@export branch=`git branch | grep \* | awk -F " " '{print $$2}'` && \
		echo "1. 获取当前分支: $$branch 需要同步分支: $*" && \
		git checkout $* >/dev/null && \
		echo "2. 切换至 $* 分支" && \
		git pull --rebase >/dev/null && \
		echo "3. 拉取最新 $* 分支：git pull --rebase" && \
		git checkout $$branch >/dev/null && \
		echo "4. 切换至 $$branch 分支" && \
		git rebase $* && \
		echo "5. $$branch 分支：git rebase $*";

# 发布目前[开发任务]分支至[测试]分支
# 当前的测试分支：test
totest:
	@export branch=`git branch | grep \* | awk -F " " '{print $$2}'` && \
		echo "1. 获取当前分支: $$branch 需要发布至测试的分支: $*" && \
		git checkout test >/dev/null && \
		echo "2. 切换至 test 分支" && \
		git pull --rebase >/dev/null && \
		echo "3. 拉取最新 test 分支：git pull --rebase" && \
		git merge $$branch && \
		echo "4. 合并 $$branch 分支 到 test 分支" && \
		git push && \
		echo "5. 发布 test 分支" && \
		git checkout $$branch >/dev/null && \
		echo "6. 切换回 $$branch 分支";

# 快速rebase同步最新master代码，遇到git push推送远端分支时报错(failed to push some refs to)，请执行 git pull --rebase origin $$branch
rebase:
	export branch=`git branch | grep \* | grep -Eo ' .+'` && \
		git checkout master && \
		git pull --rebase && \
		git checkout $$branch && \
		git rebase master;

#合并代码到gray分支
gray:
	- git branch -D gray;
	git fetch;
	export branch=`git branch | grep \* | grep -Eo ' .+'` && \
		echo "当前分支: $$branch" && \
		git checkout gray && \
		git pull --rebase && \
		git merge origin/master && \
		echo "merge: \033[0;31morigin/master\033[0m" && \
		git merge $$branch && \
		echo "merge: \033[0;31m$$branch\033[0m" && \
		git push && \
		git checkout $$branch;

#合并代码到测试分支到t[1,2,3,4,5,6,7,10]，遇到冲突请手动解决冲突
t%:
	@echo "当前对应环境名称: env0$* 分支：t$*";
	- git branch -D t$*;
	git fetch;
	export branch=`git branch | grep \* | grep -Eo ' .+'` && \
		echo "当前分支: $$branch" && \
		git checkout t$* && \
		git pull --rebase && \
		git merge origin/master && \
		echo "merge: \033[0;31morigin/master\033[0m" && \
		git merge $$branch && \
		echo "merge: \033[0;31m$$branch\033[0m" && \
		git push && \
		git checkout $$branch;
