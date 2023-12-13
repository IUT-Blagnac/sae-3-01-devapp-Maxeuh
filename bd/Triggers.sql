CREATE TRIGGER check_dateExpCB
BEFORE INSERT ON Cartebleue
FOR EACH ROW
BEGIN
    IF NEW.dateExpCB <> DATE_FORMAT(NEW.dateExpCB, '%Y-%m-%d') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La date d''expiration de la carte bleue doit être au format YYYY/MM/DD.';
    END IF;
END;
//

CREATE TRIGGER check_dateNaissanceClient
BEFORE INSERT ON Client
FOR EACH ROW
BEGIN
    IF NEW.dateNaissanceClient <> DATE_FORMAT(NEW.dateNaissanceClient, '%Y-%m-%d') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La date de naissance du client doit être au format YYYY/MM/DD.';
    END IF;
END;
//

CREATE TRIGGER check_tailleProduit
BEFORE INSERT ON Produit
FOR EACH ROW
BEGIN
    IF NEW.tailleProduit NOT IN ('XS', 'S', 'M', 'L', 'XL', 'XXL') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La taille du produit doit être XS, S, M, L, XL ou XXL.';
    END IF;
END;
//

CREATE TRIGGER check_dateCommande
BEFORE INSERT ON Commande
FOR EACH ROW
BEGIN
    IF NEW.dateCommande <> DATE_FORMAT(NEW.dateCommande, '%Y-%m-%d') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La date de commande doit être au format YYYY/MM/DD.';
    END IF;
END;
//

CREATE TRIGGER check_note
BEFORE INSERT ON Avis
FOR EACH ROW
BEGIN
    IF NEW.note NOT BETWEEN 0 AND 5 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La note doit être comprise entre 0 et 5.';
    END IF;
END;
//

CREATE TRIGGER check_tailleFR_and_tailleACommander
BEFORE INSERT ON GuideTaille
FOR EACH ROW
BEGIN
    IF NEW.tailleFR NOT IN ('XS', 'S', 'M', 'L', 'XL', 'XXL') OR NEW.tailleACommander NOT IN ('XS', 'S', 'M', 'L', 'XL', 'XXL') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Les tailles FR et à commander doivent être XS, S, M, L, XL ou XXL.';
    END IF;
END;
//

CREATE TRIGGER check_tailleSelonAgeEtSexe
BEFORE INSERT ON CategTaille
FOR EACH ROW
BEGIN
    IF NEW.tailleSelonAgeEtSexe NOT IN ('XS', 'S', 'M', 'L', 'XL', 'XXL') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La taille selon l''âge et le sexe doit être XS, S, M, L, XL ou XXL.';
    END IF;
END;
//


CREATE TRIGGER check_qteStock
BEFORE INSERT ON Commander
FOR EACH ROW
BEGIN
    DECLARE v_qteProduit DECIMAL(4),

    SELECT qteProduit INTO v_qteProduit
    FROM Produit
    WHERE refProduit = NEW.refProduit;

    IF NEW.qteCommandee > v_qteProduit THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La quantité commandée ne peut pas être supérieure à la quantité en stock.';
    END IF;
END;
//


CREATE TRIGGER after_insert_commander
AFTER INSERT ON Commander
FOR EACH ROW
BEGIN
    DECLARE v_qteActuelle DECIMAL(4),

    SELECT qteProduit INTO v_qteActuelle
    FROM Produit
    WHERE refProduit = NEW.refProduit;

    UPDATE Produit
    SET qteProduit = v_qteActuelle - NEW.qteCommandee
    WHERE refProduit = NEW.refProduit;
END;
//

