/*
 * Copyright (c) 2016, Codename One
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated 
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation 
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, 
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions 
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, 
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A 
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT 
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF 
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE 
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE. 
 */
package com.mycompany.gui.back;

import com.codename1.capture.Capture;
import com.codename1.components.FloatingHint;
import com.codename1.components.InfiniteProgress;
import com.codename1.components.ScaleImageLabel;
import com.codename1.ui.Button;
import com.codename1.ui.CheckBox;
import com.codename1.ui.ComboBox;
import com.codename1.ui.Command;
import com.codename1.ui.Component;
import com.codename1.ui.Container;
import com.codename1.ui.Dialog;
import com.codename1.ui.Display;
import com.codename1.ui.Form;
import com.codename1.ui.Image;
import com.codename1.ui.Label;
import com.codename1.ui.TextArea;
import com.codename1.ui.TextField;
import com.codename1.ui.Toolbar;
import com.codename1.ui.events.ActionEvent;
import com.codename1.ui.events.ActionListener;
import com.codename1.ui.layouts.BorderLayout;
import com.codename1.ui.layouts.BoxLayout;
import com.codename1.ui.layouts.FlowLayout;
import com.codename1.ui.layouts.GridLayout;
import com.codename1.ui.layouts.LayeredLayout;
import com.codename1.ui.plaf.Style;
import com.codename1.ui.util.Resources;
import com.mycompany.entities.CategorieEvent;
import com.mycompany.entities.Evenement;
import com.mycompany.entities.SessionManager;
import com.mycompany.services.ServiceCategorieEvent;
import com.mycompany.services.ServiceEvenement;
import com.mycompany.services.ServiceUser;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Vector;

/**
 * The user profile form
 *
 * @author Shai Almog
 */
public class AddEvenementFormBack extends BaseFormBack {

    private static String i;
float des ;

;

    public AddEvenementFormBack(Resources res) {
        super("AddEventForm", BoxLayout.y());
        Toolbar tb = new Toolbar(true);
        setToolbar(tb);
        getTitleArea().setUIID("Container");
        setTitle("AddEventForm");
        getContentPane().setScrollVisible(false);
        Form previous = Display.getInstance().getCurrent();
        tb.setBackCommand("", e -> previous.showBack());
        super.addSideMenu(res);

        Image img = res.getImage("profile-background.jpg");
        if (img.getHeight() > Display.getInstance().getDisplayHeight() / 3) {
            img = img.scaledHeight(Display.getInstance().getDisplayHeight() / 3);
        }
        ScaleImageLabel sl = new ScaleImageLabel(img);
        sl.setUIID("BottomPad");
        sl.setBackgroundType(Style.BACKGROUND_IMAGE_SCALED_FILL);

        Button btnValider = new Button("Valider");
//Label pp= new Label(ServiceUser.UriImage(SessionManager.getPhoto()),"PictureWhiteBackground");
        add(LayeredLayout.encloseIn(sl, BorderLayout.south(GridLayout.encloseIn(3, FlowLayout.encloseCenter()))));

        TextField nomEvent = new TextField();
        nomEvent.setUIID("TextFieldBlack");
        addStringValue("nomEvent", nomEvent);
        TextField DescriptionEvent = new TextField();
        DescriptionEvent.setUIID("TextFieldBlack");
        addStringValue("DescriptionEvent", DescriptionEvent);
        TextField LieuEvent = new TextField();
        LieuEvent.setUIID("TextFieldBlack");
        addStringValue("LieuEvent", LieuEvent);
        TextField NbrParticipantsEvent = new TextField("", "nbr", 20, TextArea.NUMERIC);
        NbrParticipantsEvent.setUIID("TextFieldBlack");
        addStringValue("NbrParticipantsEvent", NbrParticipantsEvent);
        TextField DateEvent = new TextField("", "Year-Month-Day", 40, TextArea.ANY);
        DateEvent.setUIID("TextFieldBlack");
        addStringValue("Date", DateEvent);
 /* ComboBox cb = new ComboBox();
           ServiceCategorieEvent rec1 = ServiceCategorieEvent.getInstance(); 
           for (CategorieEvent ar : rec1.getAllCategorieEvents()){
          cb.addItem(ar.getId()+"-"+ar.getNomCategorie());}
           
            cb.addActionListener(new ActionListener() {
            @Override
            public void actionPerformed(ActionEvent evt) {
                 des = cb.getSelectedItem();
            }
        });*/

        Vector<Float> vectorCommande;
        Vector<String> vectorCommandenom;
        vectorCommande = new Vector();
        vectorCommandenom = new Vector();
        ArrayList<CategorieEvent> enn = ServiceCategorieEvent.getInstance().getAllCategorieEvents();
        for (CategorieEvent eyy : enn) {
           // vectorCommandenom.add(eyy.getNomCategorie());
            vectorCommande.add(eyy.getId());
        }

        ComboBox<Float> commandes = new ComboBox<>(vectorCommande);
        commandes.setUIID("ComboBox");
        addStringValue("Commande", commandes);
        btnValider.setUIID("Valider");
        addStringValue("", btnValider);
        TextField path = new TextField("");

        btnValider.addActionListener(new ActionListener() {
            public void actionPerformed(ActionEvent evt) {
                if ((nomEvent.getText().length() == 0) || (DescriptionEvent.getText().length() == 0)) {
                    Dialog.show("Alert", "Please fill all the fields", new Command("OK"));
                } else {
                    try {
                        System.out.println(SessionManager.getId());
                        String image;
                        String Etat;
                        int NomCategorie;
                        float f = Float.parseFloat(NbrParticipantsEvent.getText());

                        Evenement t = new Evenement(nomEvent.getText(), DateEvent.getText(), DescriptionEvent.getText(), LieuEvent.getText(), image = "hh", Etat = "Incomplet", f, commandes.getSelectedItem());
                        if (ServiceEvenement.getInstance().addEvenement(t)) {
                            Dialog.show("Success", "Connection accepted", new Command("OK"));
                            new EvenementFormBack(res).show();
                            refreshTheme();

                        } else {
                            Dialog.show("ERROR", "Server error", new Command("OK"));
                        }
                    } catch (NumberFormatException e) {
                        Dialog.show("ERROR", "Status must be a number", new Command("OK"));
                    }

                }

            }
        });

    }

    private void addStringValue(String s, Component v) {
        add(BorderLayout.west(new Label(s, "PaddedLabel")).
                add(BorderLayout.CENTER, v));
        add(createLineSeparator(0xeeeeee));
    }
}
