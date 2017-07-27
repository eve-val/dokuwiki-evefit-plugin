/* DOKUWIKI:include_once scripts/clipboard.min.js */

jQuery().ready(() => {
  // Initialize clipboardjs on every evefit-copy element
  // Will add an onclick copy-to-clipboard handler for that
  // element's data-clipboard-target attribute, which is the
  // evefit-body.
  const clip = new Clipboard(`.evefit-copy`, {
    text: elem => jQuery(elem.dataset.clipboardTarget).text().replace(/^\s+|\s+$/g, '')
  });

  // Add expand/collapse handler to each evefit-expand element.
  jQuery(`.evefit-expand`).each((_, elem) => {
    const evefit_id = elem.dataset.evefitId;
    elem.onclick = () => { jQuery(`#evefit-${evefit_id}`).toggle(); };
  });
});
