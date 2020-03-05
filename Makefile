default:
	exit

database:
	bin/console doctrine:database:drop --force -n
	bin/console doctrine:database:create -n
	bin/console doctrine:schema:create -n