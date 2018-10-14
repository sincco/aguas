echo -n "procesando"
for f in `find _expedientes -type f -name "*" ! -path "*MIN*"`
do
	echo -n "."
	convert $f -resize 900x900 $f._MIN
done
echo "terminado"
