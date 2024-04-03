# Copyright 1999-2007 Gentoo Foundation
# Distributed under the terms of the GNU General Public License v2
# Ebuild based on simutrans-0.88.6.3.ebuild,v 1.1 2006/05/25 01:59:38 mr_bones_
# $Header: /var/cvsroot/gentoo-x86/games-simulation/simutrans/simutrans-0.99.07.ebuild,v 1.0 2007/01/25 22:30:38 wolverine Exp $

inherit games

DESCRIPTION="A free Transport Tycoon clone"
HOMEPAGE="http://www.simutrans.com/"
LICENSE="as-is"
SLOT="0"
KEYWORDS="-* ~amd64 ~x86"
IUSE="pak128 pakgerman"
RESTRICT="nomirror"

SRC_URI="http://64.simutrans.com/simulinux-99-17.zip
         pak128? (http://128.simutrans.com/download/07_03_04/simubase128-1-4-2.tar.bz2)
	 pakgerman? (
		   http://download.simutrans-forum.de/pak.german/pak.german_99-15.zip
		   http://download.simutrans-forum.de/pak.german/german_industrien_99-16.zip
		   http://download.simutrans-forum.de/pak.german/german_addon_99-15.zip
		)
	http://64.simutrans.com/simupak64-99-17.zip
	http://64.simutrans.com/simupak64-waste-99-16.zip
	http://64.simutrans.com/simupak64-food-99-13.zip
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
