PATH_SRC	=	.
PATH_SCRIPT	=	../src/byFile.cws
PATH_AGAT	=	agat

CW		=	codeworker

OPTIONS		=	-nologo
OPTIONS		+=	-stack 80000
OPTIONS		+=	-path `pwd`
OPTIONS		+=	-script $(PATH_SCRIPT)

MKDIR		=	mkdir -p
RM		=	rm -rf
CHOWN		=	chown -R
ECHO		=	/usr/bin/env echo -e

SUCCESS		=	$(ECHO) $< "\t[\033[32mSUCCESS\033[0m]"
FAILED		=	$(ECHO) $< "\t[\033[31mFAILED\033[0m]"

SRCS		=	$(PATH_SRC)/test01.agat		\
			$(PATH_SRC)/escienta.agat	\
			$(PATH_SRC)/doc.agat		\
			$(PATH_SRC)/caner.agat

TESTED		=	${SRCS:.agat=.kpx}

.SUFFIXES	:	.agat .kpx

.agat.kpx	:
			@$(MKDIR) $(PATH_AGAT)
			@$(CW) $(OPTIONS) -args $< && $(SUCCESS) || $(FAILED)
			@$(CW) $(OPTIONS) -args $< $@ && $(SUCCESS) || $(FAILED)
			@$(RM) *.out
#			@$(CHOWN) www:www $(PATH_AGAT)

all		:	${TESTED}

clean		:
			@$(RM) *~ *.out *.tree *.kpx *.xml $(PATH_AGAT)

re		:	clean all

.PHONY		:	all clean re
