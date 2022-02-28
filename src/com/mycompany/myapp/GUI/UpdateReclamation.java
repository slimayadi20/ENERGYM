/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package com.mycompany.myapp.GUI;

import com.codename1.l10n.ParseException;
import com.codename1.l10n.SimpleDateFormat;
import com.codename1.ui.Button;
import com.codename1.ui.Command;
import com.codename1.ui.Dialog;
import com.codename1.ui.FontImage;
import com.codename1.ui.Form;
import com.codename1.ui.TextField;
import com.codename1.ui.events.ActionEvent;
import com.codename1.ui.events.ActionListener;
import com.codename1.ui.layouts.BoxLayout;
import com.mycompany.myapp.entities.Reclamation;
import com.mycompany.myapp.services.ServiceReclamation;

/**
 *
 * @author MSI
 */
public class UpdateReclamation extends Form {

    Form current;

    public UpdateReclamation(Form previous, String titre, String contenu, String id) {
        current = this;
        try {

            setTitle("Update Reclamation");
            setLayout(BoxLayout.y());
            Form hi = new Form("Search", BoxLayout.y());
            hi.getToolbar().addMaterialCommandToLeftBar("", FontImage.MATERIAL_ARROW_BACK, ex -> previous.showBack());
            TextField titrefield = new TextField(titre, "titre de la reclamation");
            TextField contenufield = new TextField(contenu, "Contenu de la reclamation");
            Button btnValider = new Button("Update Event");
            Button del = new Button("Delete");

            btnValider.addActionListener(new ActionListener() {
                @Override
                public void actionPerformed(ActionEvent evt) {
                    if ((titrefield.getText().length() == 0) || (contenufield.getText().length() == 0)) {
                        Dialog.show("Alert", "Please fill all the fields", new Command("OK"));
                    } else {
                        try {

                            Reclamation t = new Reclamation( Integer.valueOf(id),titrefield.getText(), contenufield.getText());
                            if (ServiceReclamation.getInstance().updateReclamation(t, id)) {
                                Dialog.show("Success", "Connection accepted", new Command("OK"));
                            } else {
                                Dialog.show("ERROR", "Server error", new Command("OK"));
                            }
                        } catch (NumberFormatException e) {
                            Dialog.show("ERROR", "Status must be a number", new Command("OK"));
                        }

                    }

                }
            });
            del.addActionListener(new ActionListener() {
                @Override
                public void actionPerformed(ActionEvent evt) {
                    ServiceReclamation.getInstance().deletReclamation(id);
                    Dialog.show("Event Deleted", "OK");
                }
            });

            addAll(titrefield, contenufield, btnValider, del);
            DisplayReclamation eb = new DisplayReclamation(previous);
            getToolbar().addMaterialCommandToLeftBar("", FontImage.MATERIAL_ARROW_BACK,
                    e -> eb.showBack()); // Revenir vers l'interface précédente
        } catch (Exception ex) {
        }

    }
}
