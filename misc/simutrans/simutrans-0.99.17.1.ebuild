inherit games

DESCRIPTION="A free Transport Tycoon clone"
HOMEPAGE="http://www.simutrans.com/"
LICENSE="as-is"
SLOT="0"
KEYWORDS="-* ~amd64 ~x86"
IUSE="pak128 pakgerman pakjapan pakhajo"
RESTRICT="nomirror"

SRC_URI="http://download.sourceforge.net/sourceforge/simutrans/simulinux-99-17-1.zip
        pak128? (http://128.simutrans.com/download/07_03_04/simubase128-1-4-2.tar.bz2)
	pakjapan? (http://downloads.sourceforge.net/simutrans/pakjapan-99-17-1.zip)
	pakhajo? (http://downloads.sourceforge.net/simutrans/pakHAJO_0-99-17-1.zip) 
	pakgerman? (
		   http://downloads.sourceforge.net/simutrans/pak.german_0-99-17-1_full.zip
		   http://download.simutrans-forum.de/pak.german/pak.german_99-15.zip
		   http://download.simutrans-forum.de/pak.german/german_industrien_99-16.zip
		   http://download.simutrans-forum.de/pak.german/german_addon_99-15.zip
		)
	http://downloads.sourceforge.net/simutrans/pak64-99-17-1.zip
	http://downloads.sourceforge.net/simutrans/pak64-addon-food-99-17-1.zip
	"

DEPEND="app-arch/unzip"
RDEPEND="media-libs/libsdl
	media-libs/sdl-mixer	
	amd64? (
		app-emulation/emul-linux-x86-baselibs
		app-emulation/emul-linux-x86-xlibs
		app-emulation/emul-linux-x86-sdl
	)"

S=${WORKDIR}/${PN}

src_install() {
	local dir=${GAMES_PREFIX_OPT}/${PN}

	games_make_wrapper simutrans ./simutrans "${dir}"
	keepdir "${dir}/save"
	cp -R * "${D}/${dir}/" || die "cp failed"
	find "${D}/${dir}/"{text,font} -type f -print0 | xargs -0 chmod a-x
	prepgamesdirs
	fperms 2775 "${dir}/save"

}
