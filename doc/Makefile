XMLTO	=	xmlto
DBLATEX	=	dblatex
BROWSER	=	firefox
RM	=	rm -f

SRC	=	main.xml
OUTPUT	=	output

xhtml	:
		-$(XMLTO) -o $(OUTPUT) xhtml $(SRC)

pdf	:
		-$(DBLATEX) -o $(OUTPUT) $(SRC)

all	:	xhtml

clean	:
		-$(RM) -r $(OUTPUT)
		-$(RM) *~

re	:	clean all

run	:
		$(BROWSER) $(OUTPUT)/index.html &

.PHONY	:	all xhtml pdf clean
