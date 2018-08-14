echo -n "procesando"
for f in `find _expedientes/$1 -type f -name "*"`
do
	echo -n "."
	convert $f -resize 900x900 $f._MIN
done
echo "terminado"