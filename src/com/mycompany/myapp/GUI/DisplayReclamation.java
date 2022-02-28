/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.myapp.GUI;

import com.codename1.components.InfiniteProgress;
import com.codename1.components.MultiButton;
import com.codename1.ui.FontImage;
import com.codename1.ui.Form;
import com.mycompany.myapp.services.ServiceReclamation;
import com.codename1.components.SpanLabel;
import com.codename1.l10n.SimpleDateFormat;
import com.codename1.ui.Display;
import com.codename1.ui.EncodedImage;
import com.codename1.ui.Image;
import com.codename1.ui.URLImage;
import com.codename1.ui.layouts.BoxLayout;
import com.mycompany.myapp.entities.Reclamation;
import java.util.ArrayList;
import java.util.Date;

/**
 *
 * @author MSI
 */
public class DisplayReclamation extends Form {

    public DisplayReclamation(Form previous) {
        setTitle("La liste des reclamation");

        Form hi = new Form("Search", BoxLayout.y());
        //hi.setTitle("List tasks");
        hi.getToolbar().addMaterialCommandToLeftBar("", FontImage.MATERIAL_ARROW_BACK, ex -> previous.showBack());
        hi.add(new InfiniteProgress());

        ArrayList<Reclamation> en = ServiceReclamation.getInstance().getAllReclamations();

        Display.getInstance().scheduleBackgroundTask(() -> {

            Display.getInstance().callSerially(() -> {
                hi.removeAll();
                for (Reclamation eyy : en) {
                    MultiButton m = new MultiButton();
                    m.setTextLine1("titre: " + eyy.getTitre());
                    m.setTextLine2("contenu" + eyy.getContenu());
                    m.addActionListener((evt) -> {

                        UpdateReclamation a = new UpdateReclamation(previous,eyy.getTitre(), eyy.getContenu(), Integer.toString(eyy.getId()));
                        a.show();

                    });
                    hi.add(m);
                }
                hi.revalidate();
            });
        });
        add(hi);

    }

}
